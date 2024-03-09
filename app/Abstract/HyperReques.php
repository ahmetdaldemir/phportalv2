<?php namespace App\Abstract;

use App\Contract\HyperRequestInterface;
use App\Models\RemoteApiLog;
use App\Traits\HasErrors;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use SoapClient;
use SoapHeader;

class HyperRequest implements HyperRequestInterface
{

    use HasErrors;

    protected $statusCode = 200;
    protected $errorMessage = '';
    protected $result = true;

    protected $content;
    protected $log;

    protected $base_uri;
    protected $type;
    protected $path;
    protected string $method = 'GET';
    protected $options;
    protected $opt;

    protected $requestObjectForLog;

    public function __construct()
    {
        $this->log = new RemoteApiLog();
        $this->log->user_id = auth()->id() ?? 0;
        $this->log->request_class = static::class;
        $this->log->remote_path = $this->base_uri . '/' . $this->path;

        $this->requestObjectForLog = $this->options;
        if (
            isset($this->requestObjectForLog['headers'], $this->requestObjectForLog['headers']['content-type'])
            && $this->requestObjectForLog['headers']['content-type'] == 'application/json'
        ) {
            if (isset($this->requestObjectForLog['body'])) {
                $this->requestObjectForLog['body'] = json_decode($this->requestObjectForLog['body']);
            }
        }
        if ($this->type == 'soap') {
            $this->soap();
        } else {
            $this->rest_api();
        }
    }


    public function soap()
    {
        try {
            $headers = $this->requestObjectForLog['headers'];
            $auth = $this->requestObjectForLog['auth'];
            $connection = $this->connect($auth, $headers, $this->base_uri . $this->uri);
            dd($connection);

            $method = $this->path;
            $params = $this->objToArray(json_decode($this->body, true));
            $response = $connection->{$method}($params);
            $this->setContent($response);
            $this->setStatusCode($this->soapStatusCode($connection));
        } catch (ClientException|BadResponseException $exception) {
            $this->addError($exception->getMessage());
            $this->setStatusCode($exception->getResponse()->getStatusCode());
            $this->setContent($exception->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage(), $exception->getTrace());
            $this->setStatusCode(404);
        }
        $this->log->response = $this->ensureJson($this->getContent());

        $this->log->http_status = $this->getStatusCode() ?? 0;


        if ($this->hasErrors()) {
            $this->log->failed = true;
            $this->log->errors = json_encode($this->getErrors());
            $this->onError();
        } else {
            $this->log->failed = false;
            $this->onSuccess();
        }


        $this->log->save();
        $this->setLog($this->log);
        $this->onComplete();
    }

    public function rest_api()
    {

        try {
            $client = new Client(['base_uri' => $this->base_uri]);
            $response = $client->request($this->method, $this->path, $this->options);
            $this->setContent($response->getBody()->getContents());
            $this->setStatusCode($response->getStatusCode());
        } catch (ClientException|BadResponseException $exception) {
            $this->addError($exception->getMessage());
            $this->setStatusCode($exception->getResponse()->getStatusCode());
            $this->setContent($exception->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage(), $exception->getTrace());
            $this->setStatusCode(404);
        }

        $this->log->response = $this->ensureJson($this->getContent());

        $this->log->http_status = $this->getStatusCode() ?? 0;


        if ($this->hasErrors()) {
            $this->log->failed = true;
            $this->log->errors = json_encode($this->getErrors());
            $this->onError();
        } else {
            $this->log->failed = false;
            $this->onSuccess();
        }


        $this->log->save();
        $this->setLog($this->log);
        $this->onComplete();
    }

    public function __destruct()
    {
        if ($this->hasErrors()) {
            $errors = json_encode($this->getErrors(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            // SendAlert::dev("Request (log id: {$this->log->id}) ended with errors: " . $errors);
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return the log Model for the request.
     */
    public function getLog(): ?RemoteApiLog
    {
        return $this->log;
    }


    /**
     * @throws \SoapFault
     */
    public function connect($auth, $headers, $base_uri): SoapClient
    {
        $opts = [
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ];
        $this->opt = array("trace" => 1, 'soap_version' => SOAP_1_1, 'style' => SOAP_DOCUMENT, 'encoding' => SOAP_LITERAL, 'cache_wsdl' => WSDL_CACHE_NONE, 'stream_context' => stream_context_create($opts));

        $client = new SoapClient($base_uri, $this->opt);
        dd($client->GetTopLevelCategories());
        $AuthHeader = $auth;
        $header = new SoapHeader($headers['url'], $headers['Authentication'], $AuthHeader, false);
        $client->__setSoapHeaders([$header]);
        return $client;
    }

    private function soapStatusCode(SoapClient $client)
    {
        $clientHeader = $client->__getLastResponseHeaders();
        preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $clientHeader, $matches);
        return $matches[0];
    }

    protected function onSuccess(): void
    {
    }

    protected function onError(): void
    {
    }

    protected function onComplete(): void
    {
    }

    protected function prepareRequest(): void
    {
    }

    protected function objToArray($obj)
    {
        if (!is_object($obj) && !is_array($obj)) {
            return $obj;
        }
        foreach ($obj as $key => $value) {
            $arr[$key] = $this->objToArray($value);
        }

        return $arr;
    }


    private function ensureJson($input)
    {
        if (
            is_string($input)
            && (
                $input == 'null'
                || json_decode($input) !== null
            )
        ) {
            return $input;
        }

        return json_encode($input);
    }

    /**
     * @param mixed $statusCode
     */
    private function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param mixed $content
     */
    private function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param null $log
     */
    private function setLog($log): void
    {
        $this->log = $log;
    }
}
