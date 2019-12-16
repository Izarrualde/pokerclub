<?php

namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Entity\NotificationEntity;
use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\SolcreFramework2\Utility\Date;
use Solcre\Pokerclub\Entity\UserEntity;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Client as AndroidClient;
use sngrl\PhpFirebaseCloudMessaging\Message as AndroidMessage;
use ZendService\Apple\Apns\Client\Message as ApnClient;
use ZendService\Apple\Apns\Response\Message as ApnResponse;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Exception\RuntimeException;

class NotificationService extends BaseService
{

    protected $userService;
    protected $deviceService;
    protected $config;

    //APN Configuration
    public const APN_PEM_FILE       = '/pushcert.pem';
    public const APN_PEM_PASSPHRASE = 'com.solcre.lms';

    /**
     * NotificationService constructor.
     *
     * @param $userService
     * @param $entityManager
     * @param $deviceService
     * @param $config
     */
    public function __construct(
        EntityManager $entityManager,
        UserService $userService,
        DeviceService $deviceService,
        $config
    ) {
        parent::__construct($entityManager);
        $this->userService   = $userService;
        $this->deviceService = $deviceService;
        $this->config        = $config;
    }

    public function add($data, $strategies = null, $flush = true)
    {
        $data['type'] = $this->config['lms']['PUSH_NOTIFICATIONS']['TYPES']['NOTIFICATION_TYPE_GENERAL'];
        $data['createdDate'] = Date::current();
        /* @var $notification NotificationEntity */
        $notification = $this->prepareEntity($data);
        $this->entityManager->persist($notification);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $notification;
    }

    public function update($id, $data)
    {
        /* @var $notification NotificationEntity */
        $notification = parent::fetch($id);
        $notification->setTitle($data['title']);
        $notification->setMessage($data['message']);

        if (! empty($data['user'])) {
            $user = $this->userService->fetch($data['user']);
        } else {
            $user = null;
        }
        $notification->setUser($user);
        $this->entityManager->flush();

        return $notification;
    }

    public function fetchCountUserNotifications($userId)
    {
        $result = new ArrayCollection($this->repository->fetchCountUserNotifications($userId));

        return $result->count();
    }

    public function fetchAll($params = null, $orderBy = null)
    {
        if ($params['isAdmin']) {
            unset($params['isAdmin'], $params['userLogged']);

            return parent::fetchAll($params);
        }

        $user = $this->userService->fetchBy(
            [
                'cellphone' => $params['userLogged']
            ]
        );

        if ($user instanceof UserEntity) {
            $userNotifications = $this->repository->fetchUserNotifications($user->getId());

            foreach ($userNotifications as $userNotification) {
                if ($userNotification->getType() ===
                    $this->config['lms']['PUSH_NOTIFICATIONS']['TYPES']['NOTIFICATION_TYPE_GENERAL']) {
                    $userNotification->setRead(true);
                }

                $userNotification->setUserGeneralNotifications(null);
            }

            return $userNotifications;
        }
    }

    public function patch($id, $data, $notification)
    {
        if (empty($notification)) {
            /* @var $notification NotificationEntity */
            $notification = parent::fetch($id);
        }

        if (array_key_exists('androidDateSent', $data)) {
            $notification->setAndroidDateSent($data['androidDateSent']);
        }

        if (array_key_exists('iosDateSent', $data)) {
            $notification->setIosDateSent($data['iosDateSent']);
        }

        if (array_key_exists('windowsDateSent', $data)) {
            $notification->setWindowsDateSent($data['windowsDateSent']);
        }

        $this->entityManager->flush();

        return $notification;
    }

    public function sendAndroidNotification(int $notificationId, array $devices)
    {
        $notification = $this->fetch($notificationId);
        $title        = $notification->getTitle();
        $message      = $notification->getMessage();

        if (! empty($title)) {
            $androidData["title"] = $title;
        }

        if (! empty($message)) {
            $androidData["message"] = $message;
        }

        $androidData["priority"]          = '1';
        $androidData["content-available"] = '1';

        try {
            $serverKey = $this->config['lms']['PUSH_NOTIFICATIONS']['ANDROID']['TOKEN'];
            if (empty($serverKey)) {
                throw new \RuntimeException('Google push notification key not found', 404);
            }

            $client = new AndroidClient();
            $client->setApiKey($serverKey);
            $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

            $message = new AndroidMessage();
            $message->setPriority('high');
            $this->addRecipients($message, $devices);
            $message->setData($androidData);

            $response = $client->send($message);
            if ($response->getStatusCode() === 200) {
                return $devices;
            }
        } catch (\Exception $e) {
            unset($e);
        }

        return false;
    }

    private function addRecipients(AndroidMessage &$message, array $devices): void
    {
        foreach ($devices as $device) {
            $message->addRecipient(new Device($device['deviceToken']));
        }
    }

    public function sendIosNotification(int $notificationId, array $devices): array
    {
        $notification = $this->fetch($notificationId);
        $pemPath      = $this->config['lms']['PUSH_NOTIFICATIONS']['IOS']['PEM'];

        $client = new ApnClient();
        $client->open(ApnClient::PRODUCTION_URI, $pemPath . self::APN_PEM_FILE, self::APN_PEM_PASSPHRASE);

        //IOS Message
        $message = new Message();
        $message->setId($notification->getId());
        $message->setAlert($notification->getMessage());
        $message->setContentAvailable(1);

        foreach ($devices as $key => $device) {
            if ($this->checkIosDeviceToken($device['deviceToken'])) {
                $message->setToken($device['deviceToken']);

                try {
                    $response = $client->send($message);
                    if (! ($response->getCode() === ApnResponse::RESULT_OK)) {
                        // delete device didn't send
                        unset($devices[$key]);
                    }
                } catch (RuntimeException $e) {
                    unset($e);
                }
            } else {
                // delete device didn't send
                unset($devices[$key]);
            }
        }

        $client->close();

        return $devices;
    }

    private function checkIosDeviceToken($deviceToken): bool
    {
        return is_string($deviceToken) && ! preg_match('/[^0-9a-f]/', $deviceToken) && strlen($deviceToken) === 64;
    }
}
