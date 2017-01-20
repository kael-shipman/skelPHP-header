<?php

namespace Skel\Interfaces;







interface Config {
  function checkConfig();
  function get(string $key);
  function getExecutionProfile();
  function set(string $key, $val);
  function dump();
}

  interface AppConfig extends Config {
    function getContextRoot();
    function getPublicRoot();
  }

  interface DbConfig extends Config {
    function getDbPdo();
    function getDbContentRoot();
  }

  interface ContentSyncConfig extends Config {
    function getContentPagesDir();
  }





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
}

  interface AppDb extends Db {
    function getTemplate(string $name);
  }

  interface ContentSyncDb extends Db {
    function getContentFileList();
    function registerFileRename(string $prevPath, string $newPath);
    function filePathIsUnique(ContentFile $file);
    function fileContentIdIsUnique(ContentFile $file);
  }



interface Content {
}

  interface Page extends Content {
  }

    interface Post extends Page {
    }



interface ContentTag {
}


interface ContentFile {
}




interface ContentConverter {
  function toHtml(string $content);
  function toPlainText(string $content);
  function toMarkdown(string $content);
}

interface DataClass {
  static function restoreFromData(array $data);
  function updateFromUserInput(array $data);
  function set(string $field, $val, bool $setBySystem);
  function getChanges();
  function fieldHasChanged(string $field);
  function fieldSetBySystem(string $field);
  function getFieldsSetBySystem();
}

interface DataCollection {
}







interface Cms {
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










/** A generic, multi-event Observable interface */
interface Observable {
  /**
   * Registers an event listener
   *
   * The `$event` parameter can be any arbitrary string. Often, an application developer will call
   * `notifyListeners` at various points in his or her app. The strings that the developer uses for these
   * calls will trigger the execution of methods registered to respond to them.
   *
   * Note that, while it may be technically easier and in some cases cleaner to use a closure for a listener
   * instead of an observer/handler pair, using an observer/handler pair will usually result in better code
   * reuse and more logical design.
   *
   * @param string $event - an arbitrary string to listen for
   * @param object $observer - any object that has the method passed for $handler
   * @param string $handler - the method that should be called on $observer when $event is triggered
   *
   * @return void
   */
  function registerListener(string $event, $observer, string $handler);

  /**
   * Removes an event listener from the stack of listeners for a given event
   *
   * @param string $event - which event to remove the listener from
   * @param object $observer - any object that has the method passed for $handler
   * @param string $handler - the name of the method that would be called on $observer when $event is triggered
   *
   * @return void
   */
  function removeListener(string $event, $observer, string $handler);

  /**
   * Notifies registered listeners about triggered events
   *
   * @param string $event - an arbitrary string that specifies the event that's being triggered
   * @param array $data - an optional array of data elements to pass as arguments to the observer's event handler method
   *
   * @return bool
   */
  function notifyListeners(string $event, array $data=array());
}

  /**
   * An observable object that also provides strings
   *
   * A context is the basis for user-facing applications.
   */
  interface Context extends Observable {

    /**
     * Gets a string for the given key, returning either a provided default or a blank string
     *
     * @param string $key - The string you want to retrieve
     * @param string $default - An optional default string in case the key hasn't been set
     *
     * @return string
     */
    function str(string $key, string $default='');
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
    interface App extends Context {
      function getResponse(Request $request=null);
      function setRequest(Request $request);

      /** Generates an error response
       *
       * @param int $code  The error response code
       * @param string $str - an optional string to fit into the template
       * @return Interfaces\Response $response - the Response object, ready to send
       */
      function getError(int $code=404, string $header=null, string $text=null);

      function getContextRoot();
      function getPublicRoot();
      function getRequest();
      function getTemplate(string $name);

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






interface ErrorHandler {
  function getErrors(string $field=null);
  function numErrors(string $field=null);
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






interface Component extends \ArrayAccess {
  function getTemplate();
  function render();
  function setTemplate(Template $t);
}

interface DefinedComponent extends Component {
  function addDefinedFields(array $fields);
  function getDefinedFields();
  function removeDefinedFields(array $fields);
}

interface Template {
  function render(array $elements);
  static function renderInto(string $template, array $elements, bool $templateIsFileName);
}

interface ComponentCollection {
}


?>