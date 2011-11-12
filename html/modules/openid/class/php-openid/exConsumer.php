<?php
/**
 * An OpenID consumer
 *
 * @version $Rev$
 * @link $URL$
 */
require_once 'Auth/OpenID/Consumer.php';
class OpenID_ExConsumer extends Auth_OpenID_Consumer 
{
    /**
     * Initialize a Consumer instance.
     *
     * @param Auth_OpenID_OpenIDStore $store 
     * @param Auth_Yadis_PHPSession $session
     * @param str $consumer_cls
     */
    function OpenID_ExConsumer(&$store, $session = null, $consumer_cls = null)
    {
        $this->Auth_OpenID_Consumer($store, $session, $consumer_cls);
    }
    /**
     * Start OpenID verification process.
     * If discovery result is cached, skip doing OpenID server discovery.
     *
     * @param string $user_url Identity URL given by the user.
     * @param bool $anonymous
     * @return Auth_OpenID_AuthRequest
     */
    function begin($user_url, $anonymous=false)
    {
        $cache_file = XOOPS_CACHE_PATH . '/openid_' . md5($user_url);
        if (@filemtime($cache_file) > time() - 60*60*24) {
            if ($serialized = file_get_contents($cache_file)) {
                $endpoint = @unserialize($serialized);
                if (is_a($endpoint, 'Auth_OpenID_ServiceEndpoint')) {
                    $auth_req =& $this->beginWithoutDiscovery($endpoint);
                    if ($auth_req) {
                        return $auth_req;
                    }
                }
            }
        }

        $endpoint =& $this->doDiscover($user_url);
        if ($endpoint === null) {
            return null;
        }

        if (!$endpoint->claimed_id && in_array(Auth_OpenID_TYPE_2_0_IDP, $endpoint->type_uris)) {
            if ($fp = @fopen($cache_file, 'cb')) {
                if (flock($fp, LOCK_EX)) {
                    ftruncate($fp, 0);
                    fwrite($fp, serialize($endpoint));
                    flock($fp, LOCK_UN);
                }
                fclose($fp);
            }
        }

        return $this->beginWithoutDiscovery($endpoint);
    }

    function &doDiscover($openid_url)
    {
        $disco = $this->getDiscoveryObject($this->session,
                                           $openid_url,
                                           $this->session_key_prefix);

        // Set the 'stale' attribute of the manager.  If discovery
        // fails in a fatal way, the stale flag will cause the manager
        // to be cleaned up next time discovery is attempted.

        $m = $disco->getManager();
        $loader = new Auth_Yadis_ManagerLoader();

        if ($m) {
            if ($m->stale) {
                $disco->destroyManager();
            } else {
                $m->stale = true;
                $disco->session->set($disco->session_key,
                                     serialize($loader->toSession($m)));
            }
        }

        $endpoint = $disco->getNextService($this->discoverMethod,
                                           $this->consumer->fetcher);

        // Reset the 'stale' attribute of the manager.
        $m =& $disco->getManager();
        if ($m) {
            $m->stale = false;
            $disco->session->set($disco->session_key,
                                 serialize($loader->toSession($m)));
        }

        return $endpoint;
    }
}