<?php
/******** 
@USE : locu file
@PARAMETER : 
@RETURN : 
@USED IN PAGES : process.php
*********/
//=====================================HTTP EXCEPTION=========================================

class HttpException extends Exception
{
  public function __construct($message, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    return __CLASS__ . ': [{$this->code}]: {$this->message}\n';
  }
}

//=====================================HTTP CLIENT=========================================

class HttpApiClient
{
  private $api_key;
  private $base_url;  

  public function __construct($key,$url)
  {
    $this->api_key = $key;    
    $this->base_url = $url;     
  }

  /*
  Makes http get request to appropriate service (search, insight, etc.) using given args.
  Returns raw json response.
  */
  public function make_request($service_type, $args)
  {   
    $formatted = $args;
    $url = $this->base_url . $service_type . '?api_key=' . $this->api_key . $formatted; 
    $request = file_get_contents($url);     
    if(!$request)
    {
      throw new HttpException('bad request');
    }   
    return $request;
  }

  /*o
  Makes http get request to specified uri.
  Returns raw json response.
  */
  public function uri_request($uri)
  {   
    $request = file_get_contents($uri);   
    if(!$request)
    {
      throw new HttpException('bad request');
    }   
    return $request;
  }
}

//=====================================GENERIC CLIENT=========================================

class GenericApiClient extends HttpApiClient
{
  private $api_key;   
  private $api_url;
  public function __construct($key,$client_type)
  {
    $this->api_key = $key;    
    $this->api_url = 'https://api.locu.com/';
    $base_url = $this->api_url . '/v1_0/'. $client_type;
    parent::__construct($this->api_key,$base_url);
  }

  /*
  Queries search api with given params
  Returns associative php array of search results.
  */
  public function search($params)
  {   
    $resp = parent::make_request('search/', format_params($params));
    $data = json_decode($resp,true);
    return $data['objects'];
  }

  //warning: untested
  public function search_next($obj)
  {   
    if(in_array('meta', $obj) && in_array('next', $obj['meta']) && $obj['meta']['next'] != NULL)
    {
      $uri = $this->api_url . $obj['meta']['next'];
      $resp = parent::uri_request($uri);      
      $data = json_decode($resp,true);
      return $data['objects'];
    } 
    return array();      
  }

  /*
  Queries insight api with given params
  Returns associative php array of insights.
  */
  public function insight($params)
  {
    $resp = parent::make_request('insight/', format_params($params));   
    $data = json_decode($resp,true);
    return $data['objects'];
  }

  /*
  Queries details api with array of ids.
  Returns associative php array of details.
  */
  public function get_details($ids)
  {   
    if(count($ids) > 5)
    {
      $ids = array_slice($ids,0,5);
    }
    $params = implode(';', $ids) . '/';       
    $resp = parent::make_request($params, '');    
    $data = json_decode($resp,true);
    return $data['objects'];
  }
}

//=====================================VENUE CLIENT=========================================

class VenueApiClient extends GenericApiClient
{
  private $api_key;   

  public function __construct($key)
  {
    $this->api_key = $key;    
    $client_type = 'venue/';
    parent::__construct($this->api_key,$client_type);
  } 

  /*
  Queries details api with a venue id
  Returns associative php array of venue's menu.
  */
  public function get_menu($id)
  {   
    $data = $this->get_details($id);    
    $has_menu = $data[0]['has_menu'];
    if($has_menu)
    {
      return $data[0]['menus'];
    }
    return array();    
  }
}

//=====================================MENU ITEM CLIENT=========================================

class MenuItemApiClient extends GenericApiClient
{
  private $api_key;   

  public function __construct($key)
  {
    $this->api_key = $key;    
    $client_type = 'menu_item/';
    parent::__construct($this->api_key,$client_type);
  }
}

//=====================================MISC FUNCTIONS===========================================

function loc($lat,$lon)
{
  return $lat . ',' . $lon;
}

function bound($tl_lat,$tl_lon,$br_lat,$br_lon)
{
  return loc($tl_lat,$tl_lon) . '|' . loc($br_lat,$br_lon);
}

//converts array of params into url get paramters
function format_params($params)
{
  $formatted = '';
  foreach ($params as $key => $value) {
    $formatted .= '&' . $key . '=' . urlencode($value);
  }   
  return $formatted;
}
?>
