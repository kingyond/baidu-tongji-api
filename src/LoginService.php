<?php
/**
 * class LoginService, provide PreLogin, DoLogin, DoLogout methods
 */
namespace Baidu\Tongji;

/**
 * LoginService
 */
class LoginService {
    /**
     * @var string
     */
    private $loginUrl;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var array
     */
    private $messagse;
    /**
     * construct
     * @param string $loginUrl
     * @param string $uuid
     */
    public function __construct($loginUrl, $uuid) {
        $this->loginUrl = $loginUrl;
        $this->uuid = $uuid;
        $this->messagse = array();
    }

    /**
     * preLogin
     * @param string $userName
     * @param string $token
     * @return boolean
     */
    public function preLogin($userName, $token) {
        $this->addMessage('----------------------preLogin----------------------');
        $this->addMessage('[notice] start preLogin!');

        $preLogin = new LoginConnection();
        $preLogin->init($this->loginUrl);
        $preLoginData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'preLogin',
            'uuid' => $this->uuid,
            'request' => array(
                'osVersion' => 'windows',
                'deviceType' => 'pc',
                'clientVersion' => '1.0',
            ),
        );
        $preLogin->POST($preLoginData);

        if ($preLogin->returnCode === 0) {
            $retData = gzdecode($preLogin->retData, strlen($preLogin->retData));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['needAuthCode']) || $retArray['needAuthCode'] === true) {
                $this->addMessage("[error] preLogin return data format error: {$retData}");
                $this->addMessage('--------------------preLogin End--------------------');
                return false;
            }
            else if ($retArray['needAuthCode'] === false) {
                $this->addMessage('[notice] preLogin successfully!');
                $this->addMessage('--------------------preLogin End--------------------');
                return true;
            }
            else {
                $this->addMessage("[error] unexpected preLogin return data: {$retData}");
                $this->addMessage('--------------------preLogin End--------------------');
                return false;
            }
        }
        else {
            $this->addMessage("[error] preLogin unsuccessfully with return code: {$preLogin->returnCode}");
            $this->addMessage('--------------------preLogin End--------------------');
            return false;
        }
    }

    /**
     * doLogin
     * @param string $userName
     * @param string $password
     * @param string $token
     * @return array
     */
    public function doLogin($userName, $password, $token) {
        $this->addMessage('----------------------doLogin----------------------');
        $this->addMessage('[notice] start doLogin!');

        $doLogin = new LoginConnection();
        $doLogin->init($this->loginUrl);
        $doLoginData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'doLogin',
            'uuid' => $this->uuid,
            'request' => array(
                'password' => $password,
            ),
        );
        $doLogin->POST($doLoginData);

        if ($doLogin->returnCode === 0) {
           // $retData = gzdecode($doLogin->retData, strlen($doLogin->retData));
            $retData = gzinflate(substr($doLogin->retData,10,-8));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['retcode']) || !isset($retArray['ucid']) || !isset($retArray['st'])) {
                $this->addMessage("[error] doLogin return data format error: {$retData}");
                $this->addMessage('--------------------doLogin End--------------------');
                return null;
            }
            else if ($retArray['retcode'] === 0) {
                $this->addMessage('[notice] doLogin successfully!');
                $this->addMessage('--------------------doLogin End--------------------');
                return array(
                    'ucid' => $retArray['ucid'],
                    'st' => $retArray['st'],
                );
            }
            else {
                $this->addMessage("[error] doLogin unsuccessfully with retcode: {$retArray['retcode']}");
                $this->addMessage('--------------------doLogin End--------------------');
                return null;
            }
        }
        else {
            $this->addMessage("[error] doLogin unsuccessfully with return code: {$doLogin->returnCode}");
            $this->addMessage('--------------------doLogin End--------------------');
            return null;
        }
    }

    /**
     * doLogout
     * @param string $userName
     * @param string $token
     * @param string $ucid
     * @param string $st
     * @return boolean
     */
    public function doLogout($userName, $token, $ucid, $st) {
        $this->addMessage('----------------------doLogout----------------------');
        $this->addMessage('[notice] start doLogout!');

        $doLogout = new LoginConnection();
        $doLogout->init($this->loginUrl);
        $doLogoutData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'doLogout',
            'uuid' => $this->uuid,
            'request' => array(
                'ucid' => $ucid,
                'st' => $st,
            ),
        );
        $doLogout->POST($doLogoutData);

        if ($doLogout->returnCode === 0) {
            $retData = gzdecode($doLogout->retData, strlen($doLogout->retData));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['retcode'])) {
                $this->addMessage("[error] doLogout return data format error: {$retData}");
                $this->addMessage('--------------------doLogout End--------------------');
                return false;
            }
            else if ($retArray['retcode'] === 0 ) {
                $this->addMessage('[notice] doLogout successfully!');
                $this->addMessage('--------------------doLogout End--------------------');
                return true;
            }
            else {
                $this->addMessage("[error] doLogout unsuccessfully with retcode: {$retArray['retcode']}");
                $this->addMessage('--------------------doLogout End--------------------');
                return false;
            }
        }
        else {
            $this->addMessage("[error] doLogout unsuccessfully with return code: {$doLogout->returnCode}");
            $this->addMessage('--------------------doLogout End--------------------');
            return false;
        }
    }

    protected function addMessage($str) {
        $this->messagse[] = $str . PHP_EOL;
    }

    public function cleanMessages() {
        $this->messagse = array();
    }

    public function getMessages() {
        return $this->messagse;
    }
}
