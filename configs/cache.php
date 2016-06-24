<?php

class Cache {

        /**
         * Instance of Memcached
         *
         * @var Memcached
         */
        protected $memcached;
        protected $id;

        /**
         * Initialize Memcahced instance
         * 
         * @param type $id
         * @throws Exception
         */
        public function __construct($id = null) {
                // Make sure extension is available at the run-time
                if (!extension_loaded('memcached')) {
                        throw new Exception('Memcached extension failed to load.');
                }
                $this->id = $id;
                $this->memcached = new Memcached($id);

                $this->instance()->setOption(Memcached::OPT_PREFIX_KEY, $id);
                $this->instance()->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
                $this->instance()->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
                $this->instance()->setOption(Memcached::OPT_NO_BLOCK, true);
                $this->instance()->setOption(Memcached::OPT_TCP_NODELAY, true);
                $this->instance()->setOption(Memcached::OPT_COMPRESSION, true);
                $this->instance()->setOption(Memcached::OPT_CONNECT_TIMEOUT, 2);

                $this->connect();
        }

        /**
         *  Add a server to the server pool
         * 
         * @return type
         */
        public function connect() {
                $servers = array(
                        array('127.0.0.1', 11211, 33),
                        array('127.0.0.1', 11212, 67)
                );

                if (!is_null($servers)) {
                        // Since we are using persistent connections, make sure servers are not
                        // reloaded
                        if (!count($this->instance()->getServerList())) {
                                //Add a server to the server pool
                                $this->instance()->addServers($servers);
                        }
                }
                return $this->instance();
                //return $this->memcached->addServers($servers);
        }

        /**
         * Retrieve instance of Memcached 
         *
         * @return Memcached
         */
        public function instance() {
                return $this->memcached;
        }

        /**
         * Get key from cache
         * 
         * @param type $keys
         * @return boolean
         */
        public function get($keys) {

                // Prevent multi-get requests if array only contains a single key
                if (is_array($keys)) {
                        if (count($keys) == 1) {
                                $keys = array_shift($keys);
                                //$isDiverted = true;
                        }
                }

                // Determine method of retrieval
                $resource = (is_array($keys) ? $this->getArray($keys) : $this->getSimple($keys));

                return $resource;
        }

        /**
         * Retrive simple cache
         * 
         * @param type $key
         * @return boolean
         */
        protected function getSimple($key) {

                // Attempt to retrieve record within cache pool
                $resource = $this->instance()->get($key);
                if ($this->instance()->getResultCode() == Memcached::RES_SUCCESS) {
                        return $resource;
                }

                return false;
        }

        /**
         * Retrieve multiple items
         * 
         * @param array $keys
         * @return boolean
         */
        protected function getArray(array $keys) {
                // Initialize variables

                $results = array();

                // Look up keys within cache pool
                $resources = $this->instance()->getMulti(array_values($keys));
                if ($this->instance()->getResultCode() == Memcached::RES_SUCCESS) {
                        foreach ($resources as $key => $resource) {
                                $results[$key] = $resource;
                        }
                }
                // If we got some of the results, let's return them
                if ($results) {
                        return $results;
                }

                return false;
        }

        /**
         * Store an item
         * 
         * @param type $key
         * @param type $resource
         * @param type $ttl
         * @return boolean
         */
        public function set($key, $resource, $ttl = 3600) {

                // Save our data within cache pool
                if ($this->instance()->set($key, $resource, $ttl)) {

                        return true;
                }
                return false;
        }

        /**
         * Store multiple items
         * 
         * @param type $array
         * @param type $ttl
         * @return boolean
         */
        public function setMulti($array, $ttl = 3600) {

                // Save our data within cache pool
                if ($this->instance()->setMulti($array, $ttl)) {

                        return true;
                }
                return false;
        }

        /**
         * Add Key to cache
         * 
         * @param type $key
         * @param type $resource
         * @param type $ttl
         * @return boolean
         */
        public function add($key, $resource, $ttl = 3600) {
                // Save our data within cache pool
                if ($this->instance()->add($key, $resource, $ttl)) {

                        return true;
                }

                return false;
        }

        /**
         * Delete key from cache
         * 
         * @param type $key
         * @return boolean
         */
        public function delete($key) {
                // delete our data within cache pool
                if ($this->instance()->delete($key)) {

                        return true;
                }

                return false;
        }
	
	/**
         * Delete Multiple items from memcached
         * 
         * @param type $keys
         * @return boolean
         */
        public function deleteMulti($keys) {
                if ($this->instance()->deleteMulti($keys)) {
                        return true;
                }
        }

        /**
         * Replace the item under an existing key
         * 
         * @param type $key
         * @param type $resource
         * @param type $ttl
         * @return boolean
         */
        public function replace($key, $resource, $ttl = 3600) {
                // Save our data within cache poo
                if ($this->instance()->replace($key, $resource, $ttl)) {

                        return true;
                }
                return false;
        }

        /**
         * Append data to an existing item
         * 
         * @param type $key
         * @param type $value
         * @return boolean
         */
        public function append($key, $value) {
                $this->instance()->setOption(Memcached::OPT_COMPRESSION, false);
                if ($this->instance()->append($key, $value)) {
                        return true;
                }
                return false;
        }

        /**
         * Invalidate all items in the cache
         * 
         * @param type $ttl
         */
        public function flush($ttl = 10) {
                if ($this->instance()->flush($ttl)) {
                        return true;
                }
                return false;
        }

	/**
         * Get all keys
         * 
         * @return type
         */
        public function getAllKeys(){ 
		//$this->instance()->settOption(Memcached::OPT_BINARY_PROTOCOL,false);
                return $this->instance()->getAllKeys();
        }

	public function deletekeysbyprefix($prefix = false) { 
                $default_prefix = $this->instance()->getOption(Memcached::OPT_PREFIX_KEY);
                $keys = $this->getAllKeys();
                //print_r($keys);

                if(!empty($default_prefix )){

                        $reg_prefix = '/^'.$prefix.'/';
                        $mem_array = preg_grep($reg_prefix, $keys);
                        
                        $exp = '/^'.$prefix.'0/';
                        $keys = preg_filter($exp, '$1', $mem_array);    
                }else{
                        $reg_prefix = '/^'.$prefix.'/';
                        $keys = $mem_array = preg_grep($reg_prefix, $keys);
                        //$keys = array_values($)
                }

		
                if(!empty($keys)){
                        $this->deleteMulti($keys);
                }
                //print_r($keys);
                /*if ($prefix !== false) {
                        foreach ($keys as $index => $key) {
                                if (strpos($key, $prefix) !== 0) {
                                        unset($keys[$index]);
                                } else {
                                        $this->instance()->delete($key);
                                }
                        }
                }
                return $keys;*/
        }


}

if (!defined('CACHE_5_MIN')) {
        define('CACHE_5_MIN', 5 * 60); //5 min cache
}
if (!defined('CACHE_10_MIN')) {
        define('CACHE_10_MIN', 10 * 60); //10 min cache
}
if (!defined('CACHE_20_MIN')) {
        define('CACHE_20_MIN', 20 * 60); //20 min cache
}
if (!defined('CACHE_30_MIN')) {
        define('CACHE_30_MIN', 30 * 60); //30 min cache
}
