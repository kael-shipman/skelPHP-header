<?php

namespace Skel\Interfaces;






interface Authorizer {
  function requestIsAuthorized(AuthenticatedRequest $r, $action);
}






/**
 * A generic database interface
 *
 * In the case of Skel, this is used to craft a simple data abstraction layer
 * over what is usually an extension of a PDO implementation like SQLite3.
 */
interface Db {
  public function getString(string $key);
  public function getStrings();
  public function save(string $objectName, array $data);
  /**
   * Set a value
   *
   * @param string $table - The name of the table that the value pertains to
   * @param string $key - the value's key
   * @param string|int|boolean|serializable $value - the value to write
   */
  public function setValue(string $table, string $key, $newValue);
}






/**
 * A generic interface for creating and interacting with URIs
 *
 * Loosely modeled after the Java Uri implementation
 */

interface Uri {
  /** public constructor creates parses initial URI **/
  public function __construct(string $uri, Uri $relativeReference=null);

  /** Get the fragment part of the uri */
  public function getFragment();

  /** Get the host part of the uri */
  public function getHost();

  /** Get the path part of the uri (always begins with `/`) */
  public function getPath();

  /** Get the port part of the uri */
  public function getPort();

  /** Get the query part of the uri in array form */
  public function getQueryArray();

  /** Get the query part of the uri in string form */
  public function getQueryString();

  /** Get the scheme part of the uri (e.g., `https`) */
  public function getScheme();

  /**
   * Remove the matched query arguments
   *
   * @param array $arrayToRemove - a string-indexed array of arbitrary depth, the keys of which
   * will be removed from the query if matched.
   *
   * Example:
   *
   * The following would remove user[name]=pete and options[contact_methods][email]=pete@ex.com from
   * the uri https://mysite.com/sample/uri?user[alias]=mr.pete&user[name]=pete&toc=1&options[contact_methods][email]=pete@ex.com
   *
   *     $myUri->removeFromQuery(array(
   *        'user' => array(
   *          'name' => false
   *        ),
   *        'options' => array(
   *          'contact_methods' => array(
   *            'email' => false
   *          )
   *        )
   *     ));
   */
  public function removeFromQuery(array $arrayToRemove); 

  /** Set the fragment part of the uri */
  public function setFragment(string $frag);

  /**
   * Set the query part of the uri
   * 
   * @param string|array $query
   */
  public function setQuery($query);

  /** Get a string representation of the URI */
  public function toString();

  /**
   * Merge a set of key/value pairs with the existing query string
   *
   * @param array $arrayToMerge - an string-indexed array of arbitrary depth that contains
   * the values to merge.
   */
  public function updateQueryValues(array $arrayToMerge);
}







/**
 * A generic interface for Authenticated User management
 */
interface AuthenticatedUser {
  /**
   * Get an informational field from the user object
   *
   * May include first name, last name, dob, etc....
   *
   * @return mixed $info
   */
  function getInfo($key);

  /**
   * Create a user object from arbitrary credentials.
   *
   * Usually this will be username and password, but that's up to the implementor.
   *
   * @param Db $db - a data source against which to validate
   * @param array $credentials - an array containing the credentials necessary for authenticated a user
   * @return AthenticatedUser
   */
  static function createFromCredentials(Db $db, array $credentials);

  /**
   * Gets the current user's role
   *
   * This should usually default to whatever you define as the anonymous role
   *
   * @return int $role - one of the defined ROLE_* constants
   */
  function getUserRoles();
}






/**
 * A generic interface for Requests as required by the Skel framework
 *
 * This is intended to be implemented by a custom adapter class that
 * more or less just wraps Symonfy's Request class. This adapter class
 * is part of the Skel framework.
 *
 * @see http://api.symfony.com/3.1/Symfony/Component/HttpFoundation/Request.html for documentation
 */
interface Request {
  // Methods that mirror symfony Request object
  function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null);
  static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null);
  static function createFromGlobals();
  function get($key, $default=null);
  function getClientIp();
  function getMethod();
  function getPreferredLanguage();
  function getQueryString();
  function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null);
  function isXmlHttpRequest();
  function setMethod($method);
  function getUri();
}









interface AuthenticatedRequest extends Request {
  /**
   * Returns an authenticated user object, which may contain an anonymous user
   *
   * @return AuthenticatedUser
   */
  function getAuthenticatedUser();
}








/**
 * A generic interface for Responses as required by the Skel framework
 *
 * This is intended to be implemented by a custom adapter class that
 * more or less just wraps Symonfy's Response class. This adapter class
 * is part of the Skel framework.
 *
 * @see http://api.symfony.com/3.1/Symfony/Component/HttpFoundation/Response.html for documentation
 */
interface Response {
  // Methods that mirror symfony Response object
  function __construct($content = '', $status = 200, $headers = array());
  static function create($content = '', $status = 200, $headers = array());
  function prepareFromRequest(Request $request);
  function getContent();
  function getStatusCode();
  function send();
  function sendContent();
  function sendHeaders();
  function setContent($content);
  function setStatusCode($code, $text=null);
}






/** A generic route interface. This is mostly just a static data structure used in type-checking to enforce presence of expected data. */
interface Route {
  function __construct(string $pattern, $handler, string $callback, string $method);
  function execute($vars);
  function getPath(array $vars);
  function match(Request $request);
}






/** A generic interface for providing routing functionality */
interface Router {
  function addRoute(Route $route);
  function getPath($name, $vars);

  /**
   * Routes a request to a handler
   *
   * @param Request $request - the request being routed
   * @return Response $reponse
   */
  function routeRequest(Request $request);
  function getRouteByName(string $name);
}






interface UiManager {
}







interface Config {
  function get(string $key);
  function set(string $key, $val);
}








/** A generic, multi-event Observable interface */
interface Observable {
  function registerListener(string $event, $observer, string $handler);
  function removeListener(string $event, $observer, string $handler);
  function notifyListeners(string $event, $data=null);
}







/**
 * A generic interface for an Application
 *
 * This intentionally doesn't have a "register" method for registering new
 * components. I find these methods eternally confusing, as components can be added
 * from anywhere by anything, and it's hard to follow the tracks. Instead, I choose
 * to hard-code (and document) all plugins in the app-specific derivative object. This
 * provides essentially the same functionality without causing the confusion.
 *
 * The minimum functionality that an app should support is receiving, routing and
 * responding to a request, plus responding to error conditions. Thus, these are the
 * only methods required by the interface.
 */
interface App extends Observable {
  /**
   * Constructor for application
   *
   * Since at a minimum the routes (swappable) requests to logic that may use data, a database
   * and Router are the minimum requirements for initiliazing an app. Note that you can swap out
   * both of these at runtime if desired.
   *
   * @param Db $db  The database to use for this app. This doesn't actually have to be a
   * formal database, but is an abstraction layer that separates any data you might have from
   * your logic. 
   * @param Router $router  The Router object that takes the current request and routes it to
   * your program logic.
   */
  function __construct(Config $config);
  function getDb();
  function getResponse();
  function setRequest(Request $request);
  function str(string $key);

  /** Generates an error response
   *
   * @param int $code  The error response code
   * @param string $str - an optional string to fit into the template
   * @return Interfaces\Response $response - the Response object, ready to send
   */
  function getErrorResponse(int $code=404, $str=null);

  /**
   * Aborts a request, immediately sending the provided response and exiting
   *
   * @param Response $response - the response to send
   * @return void
   */
  function abort(Response $response);
}







interface AccessControlledApp extends App {
  function setAuthorizer(Authorizer $authorizer);
  function requestIsAuthorized($action);
  function requireAuthorization($action);
}







/** A generic language interface */
interface Lang {
  /** Get the language code (e.g., 'en', 'es', or 'de') */
  public function getLangCode();

  /** Get the language name (e.g., 'English', 'Español', or 'Deutsch') */
  public function getLangName();
}








/** A generic interface for providing localization functionality */
interface Localizer {
  /** Must have a public constructor that sets the default language */
  public function __construct(Lang $defaultLang);

  /** Add a language to the available languages */
  public function addLanguage(Lang $newLang);

  /** Get localized string */
  public function getString(string $key);

  /** Set the content path */
  public function setContentPath(Uri $uri);

  function getCanonicalPath(Request $r, App $app);
}







/** An interface for persisting data */
interface Persistible {
  /** Write changes to the database */
  public function persist(Db $db);

  /** Create object from data in database
   *
   * @param mixed $data - the dataset from which to create the object
   */
  public static function createFromData($data);
}

?>
