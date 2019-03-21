<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgKoowaConnect extends PlgKoowaAbstract
{
    const URL = "https://api.joomlatools.com/";

    const VERSION = '2.2.0';

    /**
     * A list of APIs that can be directly called from frontend using the following URL structure:
     *
     * index.php?option=com_ajax&plugin=connect&group=koowa&format=json&path=PATH_TO_API
     *
     * @var array
     */
    protected static $_public_apis = ['mail/validate', 'embed/iframe', 'embed/oembed'];

    protected static $_instance;

    public function __construct($dispatcher, $config = array())
    {
        parent::__construct($dispatcher, $config);

        $this->getConfig()->api_key     = trim($this->params->get('api_key'));
        $this->getConfig()->secret_key  = trim($this->params->get('secret_key'));

        static::$_instance = $this;

        $this->loadLanguage();
    }

    /**
     * @return $this
     */
    public static function getInstance()
    {
        return static::$_instance;
    }

    public static function isSupported()
    {
        $instance = static::getInstance();

        if ($instance) {
            $api    = $instance->getApiKey();
            $secret = $instance->getSecretKey();

            return !empty($api) && !empty($secret);
        }

        return false;
    }

    public function onBeforeCompileHead()
    {
        if ($this->params->get('analytics')
            && JFactory::getApplication()->input->getMethod() === 'GET'
            && JFactory::getDocument()->getType() === 'html'
            && JFactory::getApplication()->isSite()
        ) {
            $script = <<<SCRIPT
(function(name,path,ctx){var latest,prev=name!=='Keen'&&window.Keen?window.Keen:false;ctx[name]=ctx[name]||{ready:function(fn){var h=document.getElementsByTagName('head')[0],s=document.createElement('script'),w=window,loaded;s.onload=s.onerror=s.onreadystatechange=function(){if((s.readyState&&!(/^c|loade/.test(s.readyState)))||loaded){return}s.onload=s.onreadystatechange=null;loaded=1;latest=w.Keen;if(prev){w.Keen=prev}else{try{delete w.Keen}catch(e){w.Keen=void 0}}ctx[name]=latest;ctx[name].ready(fn)};s.async=1;s.src=path;h.parentNode.insertBefore(s,h)}}
})('PltT','https://d26b395fwzu5fz.cloudfront.net/keen-tracking-1.4.2.min.js',this);

  PltT.ready(function(){
    var client = new PltT({
	  host: 'track.pagelytics.io',
      projectId: '5af57311c9e77c0001639409',
      writeKey: 'B30EB30846BC7BF2A8A035DF76FEF9A39FA06719C0EB3C0D1B0412D1754D02B5C917C9F3A3E41962B635F70A70C4DC231EFD0F735F8DF9819CF92006510809EAC698F2CB210CFE8C1EB8B26AEA3A878DB858F3966BA88CC12939C50C7CC741F8'
    });

    client.initAutoTracking();
  });
SCRIPT;

            JFactory::getDocument()->addScriptDeclaration($script);
        }
    }

    /**
     * This method is used by com_ajax of Joomla to call plugins directly.
     *
     * It can be used to directly call APIs from JavaScript using the following URL structure:
     * index.php?option=com_ajax&plugin=connect&group=koowa&format=json&path=PATH_TO_API
     *
     * Additional query string parameters and the request body is directly passed upstream
     */
    public function onAjaxConnect()
    {
        if (!static::isSupported()) {
            throw new RuntimeException('Invalid credentials');
        }

        $manager = KObjectManager::getInstance();
        $request = $manager->getObject('request');
        $path    = $request->getQuery()->path;

        if ($path === 'analytics-status') {
            return $this->_handleAnalytics();
        } elseif ($path === 'activities-status') {
            return $this->_handleActivities();
        }

        if (!in_array($path, static::$_public_apis)) {
            throw new RuntimeException('Invalid public API');
        }

        $params = $request->getQuery()->toArray();

        foreach (['option', 'plugin', 'Itemid', 'group', 'format', 'path'] as $key) {
            unset($params[$key]);
        }

        $options = [
            'method' => $request->getMethod(),
            'query'  => $params,
            'data'   => $request->getMethod() === 'GET' ? null : $request->getData()->toArray()
        ];

        $response = static::sendRequest($path, $options);
        $status   = 200;

        if ($response->status_code && isset(KHttpResponse::$status_messages[$response->status_code])) {
            $status = $response->status_code;
        }

        $this->_sendResponse($response->body, $status);
    }

    protected function _sendResponse($body = null, $status = KHttpResponse::OK)
    {
        $manager = KObjectManager::getInstance();

        $manager->getObject('response', [
                'request' => $manager->getObject('request'),
                'user' => $manager->getObject('user')
            ])
            ->setStatus($status)
            ->setContent($body, 'application/json')
            ->send();
    }

    protected function _handleActivities()
    {
        $request  = $this->getObject('request');
        $status   = KHttpResponse::OK;
        $response = [];

        if (!PlgKoowaConnect::verifyToken($request->getQuery()->token)) {
            throw new RuntimeException('Invalid JWT token');
        }

        if ($request->getMethod() == 'GET') {
            $response = [
                'enabled' => (bool) $this->params->get('activities')
            ];
        }
        else
        {
            $enabled = (int) $request->getData()->enabled;

            $this->params->set('activities', $enabled);

            if ($this->_saveParameters()) {
                $response = [
                    'enabled' => (bool) $enabled
                ];
            } else {
                $status = KHttpResponse::INTERNAL_SERVER_ERROR;
            }
        }

        $this->_sendResponse(json_encode($response), $status);
    }

    protected function _handleAnalytics()
    {
        $request  = $this->getObject('request');
        $status   = KHttpResponse::OK;
        $response = [];

        if (!PlgKoowaConnect::verifyToken($request->getQuery()->token)) {
            throw new RuntimeException('Invalid JWT token');
        }

        if ($request->getMethod() == 'GET') {
            $response = [
                'enabled' => (bool) $this->params->get('analytics')
            ];
        }
        else
        {
            $enabled = (int) $request->getData()->enabled;

            $this->params->set('analytics', $enabled);

            if ($this->_saveParameters()) {
                $response = [
                    'enabled' => (bool) $enabled
                ];
            } else {
                $status = KHttpResponse::INTERNAL_SERVER_ERROR;
            }
        }

        $this->_sendResponse(json_encode($response), $status);
    }

    protected function _saveParameters()
    {
        if (!$this->getConfig()->id) {
            throw new RuntimeException('Cannot find Plugin ID');
        }

        $query = $this->getObject('database.query.update')
            ->table('extensions')
            ->values('params = :params')
            ->where('extension_id = :extension_id')
            ->bind([
                'params' => $this->params->toString(), 'extension_id' => $this->getConfig()->id
            ]);

        return $this->getObject('database.adapter.mysqli')->execute($query, KDatabase::RESULT_USE);
    }

    public function getApiKey()
    {
        return $this->getConfig()->api_key;
    }

    public function getSecretKey()
    {
        return $this->getConfig()->secret_key;
    }

    /**
     * Sends an HTTP request and returns the response
     *
     * @param  string $path Request path including the query string
     * @param  array $options Request options. Valid keys include method, data, query, and callback
     * @return string
     */
    public static function sendRequest($path, $options = array())
    {
        $curl = curl_init();

        $url = static::URL.trim($path, '/').'/';

        if (isset($options['query'])) {
            if (is_array($options['query'])) {
                $options['query'] = http_build_query($options['query'], '', '&');
            }

            $url .= '?'.$options['query'];
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST => isset($options['method']) ? strtoupper($options['method']) : "POST",
            CURLOPT_POSTFIELDS => isset($options['data']) ? json_encode($options['data']) : null,
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json",
                "Referer: ".JURI::root(),
                "Authorization: Bearer ".static::generateToken()
            ),
        ));

        if (isset($options['callback']) && is_callable($options['callback'])) {
            $callback = $options['callback'];
            $callback($curl, $path, $options);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new RuntimeException('Curl Error: '.curl_error($curl));
        }

        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (isset($status_code) && ($status_code < 200 || $status_code >= 300)) {
            throw new UnexpectedValueException('Problem in the request. Request returned '. $status_code, $status_code);
        }

        curl_close($curl);

        $result = new stdClass();
        $result->status_code = $status_code;
        $result->body        = $response;

        return $result;
    }

    /**
     * Verifies a signed JWT token
     *
     * @param string $jwt_token JWT token
     * @return boolean
     */
    public static function verifyToken($jwt_token)
    {
        /** @var KHttpTokenInterface $token */
        $token = KObjectManager::getInstance()->getObject('http.token');
        $token->fromString($jwt_token);

        return static::isSupported() && $token->verify(static::getInstance()->getSecretKey()) && !$token->isExpired();
    }

    /**
     * Returns a signed JWT token for the current API key in plugin settings
     *
     * @return string
     */
    public static function generateToken()
    {
        /** @var KHttpTokenInterface $token */
        $token = KObjectManager::getInstance()->getObject('http.token');
        $date  = new DateTime('now', new DateTimeZone('UTC'));

        return $token
            ->setSubject(static::getInstance()->getApiKey())
            ->setExpireTime($date->modify('+1 hours'))
            ->sign(static::getInstance()->getSecretKey());
    }

    /**
     * Returns if the site is running on localhost
     *
     * @return string
     */
    public static function isLocal()
    {
        static $local_hosts = array('localhost', '127.0.0.1', '::1');

        $url  = KObjectManager::getInstance()->getObject('request')->getUrl();
        $host = $url->host;

        if (in_array($host, $local_hosts)) {
            return true;
        }

        // Returns true if host is an IP address
        if (ip2long($host)) {
            return (filter_var($host, FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 |
                    FILTER_FLAG_IPV6 |
                    FILTER_FLAG_NO_PRIV_RANGE |
                    FILTER_FLAG_NO_RES_RANGE) === false);
        }
        else {
            // If no TLD is present, it's definitely local
            if (strpos($host, '.') === false) {
                return true;
            }

            return preg_match('/(?:\.)(local|localhost|test|example|invalid|dev|box|intern|internal)$/', $host) === 1;
        }
    }
}
