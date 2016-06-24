<?php
/**
 * @uses Database connection using AdoDb Connection
 * @param 
 * @used in pages : every page
 * 
 */

require_once LIBRARY."/ADOdb-master/adodb.inc.php";
require_once LIBRARY."/ADOdb-master/adodb-errorhandler.inc.php";
//require_once LIBRARY."/adodb/adodb.inc.php";
//require_once LIBRARY."/adodb/adodb-errorhandler.inc.php";
//$ADODB_CACHE_DIR = ROOT.'/ADODB_cache';
//$GLOBALS['ADODB_CACHE_DIR'] =ROOT.'/ADODB_cache';

/**
 * This Class Works As Bridge Between AdoDb Connection and Other Classes
 *
 */
class Database {

        private $DbHost;
        private $DbName;
        private $DbUser;
        private $DbPwd;
        private $DbType;
        static $Counter = 0;
        private $Conn;
        static $SelfInstance;
        static $writeInstance;

        /**
         * Constructor of the Class
         * This Will also set ADODB Connection Object in its Property
         *
         */
        public function __construct($para = "") {
                $this->DbHost = DATABASE_HOST;
                $this->DbName = DATABASE_NAME;
                $this->DbUser = DATABASE_USER;
                $this->DbPwd = DATABASE_PASSWORD;
                $this->DbType = DATABASE_TYPE;
                $this->Conn = ADONewConnection($this->DbType);

                if ($para == "") { //read
                        $this->Conn->Connect($this->DbHost, $this->DbUser, $this->DbPwd, $this->DbName);
                } else { //write
                        $this->Conn->Connect($this->DbHost, $this->DbUser, $this->DbPwd, $this->DbName);
                }


                $this->Conn->EXECUTE("set names 'utf8'");
                $this->Conn->debug = false;
                self::$Counter++;
                //print "<li>Connection: Open ".self::$Counter."</li>";
        }

        static function server_config($pool) {
		if($pool == ''){
			$pool ='read';		
		}
               /* $config = array(
                    'read' => array("mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412"),
                    'write' => array("mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412"),
                    'primary' => array(
                        "mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412",
                        "mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412"
                    ),
                    'batch' => array("mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412"),
                    'comments' => array("mysqlt://ul23ntp45:" . rawurlencode('fdf@@3%3ds!@d') . "@localhost/DBM23985412",
                    ),
                );*/


		/*test scanflip database configuration*/
		$config = array(
                    'read' => array("mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306"),
                    'write' => array("mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306"),
                    'primary' => array(
                        "mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306",
                        "mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306"
                    ),
                    'batch' => array("mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306"),
                    'comments' => array("mysqli://root:" . rawurlencode('root') . "@localhost/scanflip?persist=0&port=3306",
                    ),
                );

		/*$config = array(
                    'read' => array("mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip"),
                    'write' => array("mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip"),
                    'primary' => array(
                        "mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip",
                        "mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip"
                    ),
                    'batch' => array("mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip"),
                    'comments' => array("mysql://root:" . rawurlencode('scanflip@123') . "@localhost/test_scanflip",
                    ),
                );*/

                return $config[$pool];
        }

        /**
         * This function Check if there is an instance of this class already avaialable
         * Then return ADODB connection Object from that instance.
         * Otherwise try to create new instance and return ADODB connection
         *
         * @return ADODB Connection Object
         */
        static function ConnectDb($para) {
                //print "<li>Attemp To Open Connection</li>";   
                if ($para == "write") {
                        if (!isset(self::$writeInstance)) {
                                $c = __CLASS__;
                                self::$writeInstance = new $c("write");
                        }
                        return self::$writeInstance->Conn;
                } else {
                        if (!isset(self::$SelfInstance)) {
                                $c = __CLASS__;
                                self::$SelfInstance = new $c;
                        } // if

                        return self::$SelfInstance->Conn;
                }
        }

        static function getConnectDb($pool) {
                $servers = self::server_config($pool);
                $connection = false;
                // Keep trying to make a connection:
                while (!$connection && count($servers)) {
                        $key = array_rand($servers);
                        try {
                                $connection = NewADOConnection($servers[$key]);
				/*$connection->memCache = true;
                                $connection->memCacheHost = array('67.228.220.60'); /// $db->memCacheHost = $ip1; will work too
                                $connection->memCachePort = 11211; /// this is default memCache port
                                $connection->memCacheCompress = false; /// Use 'true' to store the item compressed (uses zlib)*/

                                $connection->EXECUTE("set names 'utf8'");
                        } catch (Exception $e) {
                                echo $e;
                        }

                        if (!$connection) {
                                // Couldn’t connect to this server, so remove it:
                                unset($servers[$key]);
                        }
                        
                }

                // If we never connected to any database, throw an exception:
                /* if (!$connection) {
                  throw new Exception("Failed Pool: {$pool}");
                  } */
                return $connection;
        }

        /**
         * Desctruct of the database class
         *
         */
        public function __destruct() {
                unset($this->DbHost);
                unset($this->DbName);
                unset($this->DbUser);
                unset($this->DbPwd);
                unset($this->DbType);

                if (isset($this->Conn)) {
                        $this->Conn->Close();
                        //print "<li>Connection Closed</li>";
                }
        }

}

?>
