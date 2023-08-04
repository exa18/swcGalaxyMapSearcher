<?
/*
*** http://php.net/manual/en/class.pdo.php#117697

define('DB_MAIN', 'localhost|user1|pa55word|db1');

*** Connect to database db1
$db = new mydb(DB_MAIN);

*** Request "SELECT * FROM table1 WHERE a=16 AND b=22"
*** Get an array of stdClass's
$rows = $db->fetchAll('SELECT * FROM table1 WHERE a=? AND b=?', 16, 22);

*/
class mydb{

    private static $databases;
    private $connection;

	public function __construct($connDetails){
        list($host, $user, $pass, $dbname, $charset) = explode('|', $connDetails);
        $name = $user . $dbname;
        if(!is_object(self::$databases[$name])){
            if (empty($charset)) { $charset = 'utf8'; }
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            self::$databases[$name] = new PDO($dsn, $user, $pass);
        }
        $this->connection = self::$databases[$name];
    }
   
    public function fetch($sql,$no=true){
        $args = func_get_args();
        array_shift($args);
        $statement = $this->connection->prepare($sql);       
        $statement->execute($args);
        $r = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($r)==1 && $no) { $r = $r[0]; }
        return $r;
    }
    public function query($sql){
        $statement = $this->connection->prepare($sql);       
        $statement->execute();
        //return $statement;
    }
    public function build($string, $array) {
    /*
    
        Sample query string
		$query = "UPDATE users SET name = :user_name WHERE id = :user_id";
		
		Sample parameters
		$params = [':user_name' => 'foobear', ':user_id' => 1001];
		
    */
	    //Get the key lengths for each of the array elements.
	    $keys = array_map('strlen', array_keys($array));
	    //Sort the array by string length so the longest strings are replaced first.
	    array_multisort($keys, SORT_DESC, $array);
	
	    foreach($array as $k => $v) {
	        //Quote non-numeric values.
	        $replacement = is_numeric($v) ? $v : "'{$v}'";
	        //Replace the needle.
	        $string = str_replace($k, $replacement, $string);
	    }
	    return $string;
	}
}

?>