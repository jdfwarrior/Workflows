<?php

namespace AlfredApp;

class Client
{
    /**
     * @var resource
     */
    private $curlHandle;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->setUrl($url);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $this->initCurlHandle();
        curl_setopt($this->curlHandle, CURLOPT_HTTPGET, true);
        return $this->getResponse();
    }

    /**
     * @param array $postData
     * @return mixed
     */
    public function post(array $postData = [])
    {
        $this->initCurlHandle();
        curl_setopt($this->curlHandle, CURLOPT_POST, true);
        $this->addPostData($postData);
        return $this->getResponse();
    }

    /**
     * @param array $deleteData
     * @return mixed
     */
    public function delete(array $deleteData = [])
    {
        $this->initCurlHandle();
        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        $this->addPostData($deleteData);
        return $this->getResponse();
    }

    /**
     * @param array $putData
     * @return mixed
     */
    public function put(array $putData = [])
    {
        $this->initCurlHandle();
        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, "PUT");
        $this->addPostData($putData);

        return $this->getResponse();
    }

    /**
     * @return void
     */
    private function initCurlHandle()
    {
        if (! is_resource($this->curlHandle)) {
            $this->curlHandle = curl_init();
            curl_setopt($this->curlHandle, CURLOPT_HEADER, false);
            curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        }
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->initCurlHandle();
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
    }

    /**
     * @return void
     */
    private function close()
    {
        curl_close($this->curlHandle);
    }

    /**
     * @return mixed
     */
    private function getResponse()
    {
        $returnContent = curl_exec($this->curlHandle);
        $this->close();
        return $returnContent;
    }

    /**
     * @param array $postData
     * @return void
     */
    private function addPostData(array $postData)
    {
        if (empty($postData)) {
            return;
        }
        $fields = json_encode($postData);
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, [
            'Content-Length: ' . mb_strlen($fields),
            'Content-Type: application/json'
        ]);
    }
}