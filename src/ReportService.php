<?php
/**
 * class ReportService
 */
namespace Baidu\Tongji;

/**
 * ReportService
 */
class ReportService {
    private $apiUrl;
    private $userName;
    private $token;
    private $ucid;
    private $st;
    private $uuid;
    private $account_type;

    /**
     * @var array
     */
    private $messagse;
    /**
     * construct
     * @param string $apiUrl
     * @param string $userName
     * @param string $token
     * @param string $ucid
     * @param string $st
     */
    public function __construct($apiUrl, $userName, $token, $ucid, $st, $uuid, $account_type) {
        $this->apiUrl = $apiUrl;
        $this->userName = $userName;
        $this->token = $token;
        $this->ucid = $ucid;
        $this->st = $st;
        $this->uuid = $uuid;
        $this->account_type = $account_type;
        $this->messagse = array();
    }

    /**
     * get site list
     * @return array
     */
    public function getSiteList() {
        $this->addMessage('----------------------get site list----------------------');
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getSiteList', $this->ucid, $this->uuid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->account_type,
            ),
            'body' => null,
        );
        $apiConnection->POST($apiConnectionData);

        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
    }

    /**
     * get data
     * @param array $parameters
     * @return array
     */
    public function getData($parameters) {
        $this->addMessage('----------------------get data----------------------');
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getData', $this->ucid, $this->uuid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->account_type,
            ),
            'body' => $parameters,
        );
        $apiConnection->POST($apiConnectionData);

        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
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
