<?php

namespace Skel\Interfaces;






/**
 * `Config` -- An interface that defines a standard way to load configurations for various
 * components
 *
 * `Config` and its derivatives are designed to be aggregated in a single Config class
 * that is used for all of the various components in an application. For example, if you
 * have an App, a Db, and a Cms (which expects an implementation of `DbConfig`, you can
 * create a `Config` class that implements both the `AppConfig` and `DbConfig` interfaces
 * (both of which are derivatives of the `Config` base interface) and pass an instance
 * of this class to your `App`, your `Db` and your `Cms` instances.
 *
 * Of course, as new components emerge, new `*Config` interfaces will be necessary and can
 * be added. Separating out all these interfaces allows you to mix and match in the event
 * that you don't necessarily need this or that specific component and configuration, while
 * still ensuring that your application can alert you when it's lacking certain important
 * configurations.
 */

interface Config {
  /**
   * Provides a way to verify whether or not the configuration has the correct keys in place
   * to fulfill all of its requirements. Should throw an exception in the event that certain
   * keys are lacking.
   *
   * @return void
   */
  function checkConfig();

  /**
   * Gets the currently configured execution profile
   *
   * @return int -- an integer that should match one of the defined PROFILE_* class constants.
   */
  function getExecutionProfile();

  /**
   * Provides a way to view all current configuration values for debugging purposes
   *
   * @return void
   */ 
  function dump();
}

  interface AppConfig extends Config {
    /**
     * Returns the full directory in which the app (or "context") is currently executing.
     *
     * In most web applications, this will usually one directory above the web root.
     *
     * @return string -- A string representing the root directory of the app.
     */
    function getContextRoot();

    /**
     * Returns the full directory from which public files are served.
     *
     * In most web applications, this would be the web root, i.e., `public_html`, `www`, or similar.
     *
     * @return string -- A string representing the public root directory.
     */
    function getPublicRoot();

    /**
     * Returns the full directory in which templates are stored
     *
     * @return string -- A string representing the template directory
     */
    function getTemplateDir();
  }

  interface DbConfig extends Config {
    /**
     * Returns an instance of a PDO.
     *
     * This is so that you can create and configure the PDO in your global and local configuration
     * files, rather than have your classes make assumptions about what type of database you want.
     *
     * @return PDO
     */
    function getDbPdo();

    /**
     * Returns the full directory in which user-facing content is to be found.
     *
     * This directory will usually have a strings file as well as assets and other human-language
     * content files.
     *
     * @return string -- A string representing the full directory in which user-facing content is stored.
     */
    function getDbContentRoot();
  }

  interface ContentSyncConfig extends Config {
    /**
     * Returns the full directory in which the text files representing Cms content pages are stored.
     *
     * @return string -- A string representing the full directory in which Cms content pages are stored.
     */
    function getContentPagesDir();
  }






/**
 * A minimal database interface
 *
 * This interface is intentionally sparse, since it is intended to provide the foundation for a highly
 * declarative data layer. While there are some very basic fundamentals (listed across the interfaces below),
 * the majority of database methods will be specific to the application that the database serves and
 * thus will be defined in the specific context of those applications.
 */
interface Db {
  /**
   * Returns a string for a given key. This is used to provide future compatibility for I18n.
   *
   * @param string $key -- the key of the text to retrieve
   * @return string -- the value of the text
   */
  public function getString(string $key);

  /**
   * Returns a dump of all available strings in array form.
   *
   * @return array -- a key-indexed array of string values
   */
  public function getStrings();
}

  interface AppDb extends Db {
    /**
     * Returns an array of menu items
     *
     * For now, this function returns an array of arbitrary format, though it's probable that in the future
     * it will return an array of `MenuItem` objects (an interface that has yet to be defined).
     *
     * Also, for now, it only returns a single-dimensional menu, rather than a hierarchical menu.
     */
    function getMenuItems(string $menuName=null);
  }

  /**
   * Interface for a database that serves the `ContentSynchronizerLib`
   *
   * This provides various methods for managing `ContentFile` records.
   */
  interface ContentSyncDb extends Db {
    /**
     * Gets the current list of known ContentFiles
     *
     * @return DataCollection -- a collection of ContentFile objects
     */
    function getContentFileList();

    /**
     * Register in the database that a file has been renamed.
     *
     * This is triggered when the system finds a file that appears to point to content that's already handled
     * by another ContentFile that appears no longer to exist (which is to say, it encounters an old file
     * with a new name).
     *
     * @param string $prevPath -- the previous path at which the content was found
     * @param string $newPath -- the new path at which the content is found
     * @return void
     */
    function registerFileRename(string $prevPath, string $newPath);

    /**
     * Indicates whether a ContentFile is already registered in the database.
     *
     * @param ContentFile $file -- the file to test
     * @return bool
     */
    function filePathIsUnique(ContentFile $file);

    /**
     * Checks to see if there are any other ContentFile records that are already managing the content with this ID
     *
     * @param ContentFile $file -- The content file in question
     * @return bool
     */
    function fileContentIdIsUnique(ContentFile $file);
  }





/**
 * This is half of an ORM solution. There is much left to be done on it, but it was already implemented in
 * part and it didn't make sense to push these methods up into the standard Db interface
 */
interface Orm {
  /**
   * Delete an object represented by a DataClass instance
   *
   * @param DataClass $object -- the object to be deleted from the database
   * @return void
   * @throws PDOException
   */
  function deleteObject(DataClass $object);

  /**
   * Save an object represented by a DataClass instance
   *
   * @param DataClass $object -- the object to be saved to the database
   * @return void
   * @throws PDOException, \Skel\InvalidDataObjectException
   */
  function saveObject(DataClass $object);
}






/**
 * An interface for a renderable Component
 *
 * This is one of the central interfaces of the Skel collection. This is the interface in which all data in Skel are represented,
 * allowing you to assign a template to a data structure, thus allowing it to later be rendered on screen. The major advantage
 * to using this over simple arrays is that you can create hierarchies of Data components for which you can continue to manipulate
 * both the data and the way the data is displayed, all the way up until the point of render. This is a crucial capability in
 * the creation of reusable libraries.
 */
interface Component extends \ArrayAccess {
  /**
   * Get the `Template` object, if any, associated with this component
   *
   * @return Template|null
   */
  function getTemplate();

  /**
   * Render the component
   *
   * In the context of web applications, this will usually result in a string, but depending on the actual Template class used,
   * it may also result in changes to an already-built on-screen interface.
   *
   * @return string|void -- Either the fully-rendered string representation of the Component or void
   */
  function render();

  /**
   * Set the Template for this component
   *
   * @param Template $t
   * @return Component -- should return itself to allow method chaining
   */
  function setTemplate(Template $t);
}


/**
 * Adds the ability to add specific, known fields to a Component
 *
 * This is used in DataClass, for example, to allow the ORM to query a Component for its "defined fields" in order to save them
 * to the database.
 */
interface DefinedComponent extends Component {
  /**
   * Add a defined set of fields to the object
   * 
   * @param array $fields -- a simple array of field names
   * @return void
   */
  function addDefinedFields(array $fields);

  /**
   * Says whether or not the given field has been officially defined
   *
   * @param string $field -- The field to query
   * @return bool
   */
  function fieldIsDefined(string $field);

  /**
   * Get all fields registered with `addDefinedFields`
   *
   * Note that defined fields can be added (and removed) by derivative classes and by the program in general, so which fields are
   * "defined" at a given time can't necessarily be assumed.
   *
   * @return array -- the fields that are currently "defined"
   */
  function getDefinedFields();

  /**
   * Remove fields that may or may not have been defined
   *
   * @param array $fields -- a simple array of field names to undefine
   * @return void
   */
  function removeDefinedFields(array $fields);
}


/**
 * An interface defining a renderable template object
 *
 * This interface is used heavily by `Component`, but it can also be used independently
 */
interface Template {
  /**
   * Render the Template with the given elements
   *
   * @param array $elements -- An associative array of elements to pass into the template for rendering
   * @return string|void -- The rendered template (or sometimes `void`, if the template is designed for a different context)
   */
  function render(array $elements);

  /**
   * Provide and render a template with elements all in one step
   *
   * @param string $template -- the template into which to render the elements
   * @param array $elements -- the elements to render into the template
   */
  static function renderInto(string $template, array $elements);
}


/**
 * A collection of Component objects
 *
 * This class provides sort of a poor-man's database cache, as you can load data components into it and then use some basic
 * querying methods provided to get specific data components out of it.
 */
interface ComponentCollection {
  /**
   * Pop a Component off the end of the collection (works like `array_pop`)
   *
   * @return Component
   */
  function pop();

  /**
   * Shift a Component off the beginning of the collection (works like `array_shift`)
   *
   * @return Component
   */
  function shift();

  /**
   * Slide a Component onto the beginning of the collection, moving all other Components to the right one space (works like `array_unshift`)
   *
   * @return int $count -- the new number of Components in the collection
   */
  function unshift(Component $c);

  /**
   * Remove the given component from the collection
   *
   * @param Component $c
   * @return void
   */
  function remove(Component $c);

  /**
   * Get the numeric index of the given component
   *
   * @param Component $c
   * @return int $i
   */
  function indexOf(Component $c);

  /**
   * Get a subset of components in the collection that match the given value for `$key`
   *
   * For example, given a collection of `Person` Components with fields `id`, `name`, `address`, you could filter on `name == 'Joe'` to retrieve
   * all Components with a `name` field equal to 'Joe'. Note that this is not intended to fulfill complex querying needs. It is only a convenience
   * for light manipulation of a collection.
   *
   * @param string $key -- The key to match
   * @param mixed $val -- The value to match. Since a Component field can contain any value, the type is not defined
   * @return array -- an array of matching Components (it would make more sense for this to be a ComponentCollection, which may happen in the future)
   */
  function filter(string $key, $val);

  /**
   * Get a cross-sectional array of all values for `$key` in the collection.
   *
   * For example, given a collection of `Person` Components with fields `id`, `name`, and `address`, calling `getColumn('name') would return an array
   * of all of the `name` values for the Components in the collection.
   *
   * @param string $key -- The key to get
   * @return array -- an array of the values of `key` for each Component in the collection
   */
  function getColumn(string $key);

  /**
   * Determine whether or not the collection contains a Component with the given key/value pair
   *
   * @param string $key -- the key to match
   * @param mixed $val -- the value to match
   * @return bool
   */
  function contains(string $key, $val);
}




/**
 * An interface for a content management system
 *
 * The idea behind this CMS is that it serve as a very basic ORM with some added querying functionality that's specific
 * to the way content is often addressed in blogs and websites.
 */
interface Cms extends Orm {
  /**
   * Return a Content object or an array of Content objects associated with the address or addresses provided
   *
   * Note: This method always returns an array when an array of addresses is given (even if there is only one
   * address in the array, and even if there is only one result), and always returns a single Content
   * object when only a single address is given (as a string).
   *
   * @param array|string $address -- the address or addresses for which to find content (string representations of URIs)
   * @return array|null|Content $content -- an instance of `Content` or any derivative thereof as determined by the
   * `dressData` function.
   * @throws PDOException
   */
  function getContentByAddress($address);

  /**
   * Return the Content object that matches the canonicalId/language pair given as arguments
   *
   * This is most often used for translations. For example, given the english language content at `/my-section/my-page`,
   * you can search for the spanish language translation by calling `getContentByCanonicalId('/my-section/my-page', 'es')`.
   * (Note that the actual value passed in as the `canonicalId` should be gotten from the actual `Content` object's
   * `getCanonicalId` method.)
   *
   * @param string $canonicalId -- the canonicalId to search
   * @param string $lang -- the language to filter for
   * @return Content|null
   */
  function getContentByCanonicalId(string $canonicalId, string $lang='en');

  /**
   * Return the Content object that matches the given id
   *
   * @param int $id
   * @return Content|null
   */
  function getContentById(int $id);

  /**
   * Return an array mapping the content class values stored in the db to actual content classes that can be instantiated
   *
   * This is a factory method that can be overridden by derivative CMS classes to provide more or different class maps. For
   * example, the base map translates 'page' to `\Skel\Page` and 'post' to `\Skel\Post`. You can add classes to this or change
   * mappings by overriding the method like so:
   *
   * ```php
   * public function getContentClasses() {
   *   return array_merge(
   *     parent::getContentClasses(),
   *     array('new-class' => '\Me\NewClass', 'post-alias' => '\Skel\Post', 'page' => '\Me\MyPage')
   *   );
   * }
   * ```
   *
   * The only stipulation is that all classes must implement the Skel `Content` interface to be usable by the Cms
   *
   * @return array $classMap
   */
  function getContentClasses();

  /**
   * Get an index of all content that is a descendent of any of the parent addresses provided, or an index of all content if
   * no parent addresses are provided.
   *
   * This function should return all *descendents* of the given parents, not just direct children.
   *
   * @param array $parent_addresses -- an array of addresses for which to construct the index
   * @param int $limit -- an optional limit
   * @param int $offset -- an optional offset
   * @param string $orderby -- an optional SQL orderby string
   * @return array $content -- an array of content objects, or an empty array if no objects are found
   */
  function getContentIndex(array $parent_addresses=null, int $limit=null, int $offset=0, $orderby='"dateCreated" DESC');

  /**
   * Given an array of tags, this function adds any that aren't in the database, then returns a collection of `ContentTag` objects
   *
   * @param array $tags -- array of tags (in human format)
   * @return DataCollection $tags -- array of `ContentTag` objects
   */
  function getOrAddTagsByName(array $tags);

  /**
   * Get all tags associated with the given Content
   *
   * @param Content $content -- the content object to get tags for
   * @return DataCollection -- a collection of `ContentTag` objects
   */
  function getContentTags(Interfaces\Content $content);

  /**
   * Get the Content object representing the parent of the given Content object
   *
   * @param Content $content -- the content object to get the parent of
   * @return Content|null -- the parent of the given object or null if root Content
   */
  function getParentOf(Interfaces\Content $content);
}






/**
 * An interface that defines a generic Data object for use in an ORM
 */
interface DataClass extends DefinedComponent, ErrorHandler {
  /**
   * Check whether the given field has been changed since last save
   *
   * @param string $field -- the field to check
   * @return bool
   */
  function fieldHasChanged(string $field);

  /**
   * Check whether the given field was set by the system (as opposed to the user)
   *
   * @param string $field -- the field to check
   * @return bool
   */
  function fieldSetBySystem(string $field);

  /**
   * Get all changed fields and their previous values
   *
   * @return array -- an associative array with fields as keys pointing to arrays containing all previous values of
   * the field since the last save
   */
  function getChanges();

  /**
   * Get the raw data representation of the object
   *
   * This should be a data array that can be used directly in the `restoreFromData` method
   *
   * @return array -- an associative array with fields as keys pointing to raw data values
   */
  function getData();

  /**
   * Get an array of all the fields that have been set by the system (as opposed to through intentional action by the user)
   *
   * @return array -- simple array of all fields set by system
   */
  function getFieldsSetBySystem();

  /**
   * Get the normalized class name for the current calling class. This is to provide a normalized way to store
   * class names in the database for later mapping to program classes.
   *
   * One way of doing this is, for example, to simply return the class name without namespaces. A slightly more robust way
   * might be to strip namespaces, then force the class name to lower case.
   *
   * @return string -- the normalized class name
   */
  static function getNormalizedClassName();

  /**
   * Get the raw data value of the field
   *
   * @param string $field -- the field whose value you want to retrieve
   * @return mixed -- The raw data value (i.e., scalar value) of the field
   */
  function getRaw(string $field);

  /**
   * Create an instance of the class using data stored in a database
   *
   * This is specifically for "restoring" a previously validated and persisted object. To create a new object from data that
   * is unvalidated, use `$obj = new DataClass(); $obj->updateFromUserInput($data);`.
   *
   * @param array $data -- an associative array of data from a database
   * @return DataClass -- an instance of DataClass or derivative (uses late static binding to create an instance of whatever class called it)
   */
  static function restoreFromData(array $data);

  /**
   * Set a field to a value, indicating whether this was an action initiated by the user or the system
   *
   * @param string $field -- The field to set
   * @param mixed $val -- The value to set it to
   * @param bool $setBySystem -- Flag indicating if this is a system-initiated action
   */
  function set(string $field, $val, bool $setBySystem);

  /**
   * Change fields according to user input
   *
   * This can be used to update an object from data that a users passes in via a web form, for example.
   *
   * @param array $data -- an associative array of unvalidated object data
   * @return DataClass -- should return `$this` to allow for method chaining
   */
  function updateFromUserInput(array $data);
}


/**
 * An interface used to manage one-to-many and many-to-many data relationships
 *
 * This is typically used to represent collections of objects under a given field in a parent object,
 * for example, `Tags` on a `Content` object. How it maps to tables in the database can be a little tricky,
 * so examples are provided below.
 */
interface DataCollection extends ComponentCollection {
  /**
   * Get the linking table name. For something like pages to tags (m2m), this might be `pagesTags`. For something
   * like cities to citizens (m2o), it might just be `people` (i.e., the `people` table, rather than a `citiesPeople`
   * table, since each person presumably only has one primary city as his or her home, and therefore it would make
   * sense to have a single `cityId` field in the `people` table).
   *
   * @return string $name -- the linking table name
   */
  public function getLinkTableName() { return $this->linkTableName; }

  /**
   * Get the parent link key. Using the tags example, this would be `pageId`, since `page` is the parent of the `tags`
   * collection. For a list of the citizens in a given city, it would be `cityId`, since `city` is the parent of the
   * `citizens` collection. In short, this is the id column associated with the **parent** of the collection in the
   * link table.
   *
   * @return string $key -- the parent linking field
   */
  public function getParentLinkKey() { return $this->parentLinkKey; }

  /**
   * Get the child table name. For the tags collection of a page (m2m), for example, this would be `tags`; for the citizens
   * of a city (a m2o relationship), it's not necessary, just leave it null.
   *
   * @return string|null $name -- the child table name or null
   */
  public function getChildTableName() { return $this->childTableName; }

  /**
   * Get the child linking key. For example, for the tags of a page (m2m), `tagId`; for the citizens of a city (m2o), leave
   * it null.
   *
   * @return string|null $key -- the name of the child link key, if applicable
   */
  public function getChildLinkKey() { return $this->childLinkKey; }

  /**
   * Set the linking table name.
   *
   * @param string $name -- the table name
   * @return DataCollection -- should return `$this` for method chaining
   */
  function setLinkTableName(string $name);

  /**
   * Set the parent link key name
   *
   * @param string $name -- the name of the parent linking key
   * @return DataCollection -- should return `$this` for method chaining
   */
  function setParentLinkKey(string $name);

  /**
   * Set the child table name, if applicable
   *
   * @param string $name -- the name of the child table
   * @return DataCollection -- should return `$this` for method chaining
   */
  function setChildTableName(string $name);

  /**
   * Set the child link key, if applicable
   *
   * @param string $name -- the name of the child linking key
   * @return DataCollection -- should return `$this` for method chaining
   */
  function setChildLinkKey(string $name);
}



/**
 * Defines a general interface for a Content object.
 *
 * This is intended to be a generic Content object that could be a string, a picture
 * a video, an article, etc. These more specific types of Content should be derivatives
 * of this generic Content interface.
 */
interface Content {
  /**
   * Get the address at which the content can be found.
   *
   * This may eventually turn into a one-to-many relationship, since it's conceivable that a single
   * piece of content would have more than one address.
   *
   * @return string $address -- A string representation of the URI where this content is found
   */
  function getAddress();

  /**
   * Get this content's ancestors
   *
   * @return array $ancestors -- an array of string URIs of this content's ancestors 
   */
  function getAncestors();

  /**
   * Get this content's canonical ID
   *
   * This would usually take the form of a URI, but that's not a strict requirement. The canonical
   * ID is simply a way have linking two or more different content objects, for example in the event
   * of a translation or other such condition. In such a case, you might have the english content item
   * `/programs` with canonical ID `/programs` linked to the spanish content item `/programas` with
   * canonical ID `/programs`. Since the canonical ID is the same, it is said that these two content
   * items represent the same essential content, but in different languages.
   *
   * @return string $canonicalId -- The arbitrary string representing the content's canonical ID
   */
  function getCanonicalId();

  /**
   * Get this content's children
   *
   * @return DataCollection $children -- a single-level DataCollection of this content's direct children
   */
  function getChildren();

  /**
   * The content
   *
   * @return string $content
   */
  function getContent();

  /**
   * Get the content class
   *
   * This can be used by the CMS or the application to map this content to a specific class of program
   * object.
   *
   * @return string $contentClass
   */
  function getContentClass();

  /**
   * Get the date on which this content was created.
   *
   * @return DateTime $date -- a `DateTime` object representing when this content was created
   */
  function getDateCreated();

  /**
   * Get the date on which this content will expire (if applicable).
   *
   * @return DateTime|null $date -- a `DateTime` object representing when this content will expire
   */
  function getDateExpired();

  /**
   * Get the date on which this content was last updated.
   *
   * In general, this should be updated only when the actual *content* is update, not attributes like
   * images and address.
   *
   * @return DateTime $date -- a `DateTime` object representing when this content was last updated
   */
  function getDateUpdated();

  /**
   * Get the image prefix for this object
   *
   * This field is usually used as the main part of the name of all asset files associated with this
   * content. For example, the content may have a representative thumbnail, and that image can be found
   * using this prefix.
   *
   * @return string $imgPrefix
   */
  function getImgPrefix();

  /**
   * Get the language of the content
   *
   * @return string $lang -- should return the 2-letter ISO 639-1 language code of the language in which
   * the content appears.
   */
  function getLang();

  /**
   * Get the address of the content's parent, if applicable
   *
   * @return string $uriAddress -- the string representation of the parent's URI
   */
  function getParentAddress();

  /**
   * Get the "slug" for the object. This is the unique, human-readable url component that serves as the 
   * "pretty ID" for the content
   *
   * @return string $slug
   */
  function getSlug();

  /**
   * Get any tags associated with this content
   *
   * @return DataCollection $tags -- a collection of `ContentTag` objects
   */
  function getTags();

  /**
   * Get the title of the content
   *
   * @return string $title
   */
  function getTitle();

  /**
   * Get whether or not the content has any children
   *
   * @return bool
   */
  function hasChildren();

  /**
   * Get whether or not the content is active
   *
   * @return bool
   */
  function isActive();
}

  /**
   * In practice, there doesn't seem to be any functional difference between
   * `Page` and more general `Content`. Still, I think it's good to have a reserved
   * interface for `Page` in case important differences emerge in the future.
   */
  interface Page extends Content {
  }

    /**
     * An interface for blog posts, as distinguished from site pages
     */
    interface Post extends Page {
      /**
       * Get the author of the post
       *
       * @return string $author
       */
      function getAuthor();
    }



/**
 * An interface representing a tag used for content
 */
interface ContentTag {
  /**
   * Get the name of the tag
   *
   * @return string $tagName
   */
  function getTagName();
}


/**
 * An interface used by ContentSynchronizerLib to define a file that represents a discrete
 * entry in the Cms.
 */
interface ContentFile {
  /**
   * The path of the file, relative to `ContentSyncConfig::getContentPagesDir`
   *
   * @return string $path
   */
  function getPath();

  /**
   * The last modification time of the file, as reported by the OS
   *
   * @return DateTime $mtime
   */
  function getMTime();

  /**
   * The Cms ID of the content that this ContentFile represents
   *
   * @return int $contentId
   */
  function getContentId();
}




/**
 * An interface for converting content from one format to another
 *
 * This is meant to be an interface implemented specifically for certain types of content.
 * For example, you might create an object called HtmlConverter which implements this interface
 * to convert HTML to Plain Text and Markdown, and you might create another object called MyConverter
 * that converts your special brand of content markup to HTML, Plain Text and Markdown, etc....
 */
interface ContentConverter {
  /**
   * Converts content to HTML
   *
   * @param string $content -- the content to be converted
   * @return string $html
   */
  function toHtml(string $content);

  /** Converts content to plain text
   *
   * @param string $content -- the content to be converted
   * @return string $plainText
   */
  function toPlainText(string $content);

  /** Converts content to Markdown
   *
   * @param string $content -- the content to be converted
   * @return string $markdown
   */
  function toMarkdown(string $content);
}







/**
 * A generic interface for creating and interacting with URIs
 *
 * Loosely modeled after the Java Uri implementation
 */

interface Uri {
  /**
   * Public constructor parses URI string, optionally using a reference URI
   *
   * @param string $uri -- a uri string, e.g., `https://google.com`, `/my/path` or `www.geocities.com?id=18`
   * @param Uri $relativeReference -- a Uri used to fill in missing parts if passing a relative string uri
   * @return Uri
   */
  public function __construct(string $uri, Uri $relativeReference=null);

  /**
   * Get the fragment part of the uri
   *
   * @return string
   */
  public function getFragment();

  /**
   * Get the host part of the uri
   *
   * @return string
   */
  public function getHost();

  /**
   * Get the path part of the uri (always begins with `/`)
   *
   * @return string
   */
  public function getPath();

  /**
   * Get the port part of the uri
   *
   * @return string
   */
  public function getPort();

  /**
   * Get the query part of the uri in array form
   *
   * @return string
   */
  public function getQueryArray();

  /**
   * Get the query part of the uri in string form
   *
   * @return string
   */
  public function getQueryString();

  /**
   * Get the scheme part of the uri (e.g., `https`)
   *
   * @return string
   */
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

  /**
   * Get a string representation of the URI
   *
   * @return string
   */
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
 * A generic interface for Requests as required by the Skel framework
 *
 * This is inspired by Symfony's HttpFoundation classes, but is intended to be much simpler
 *
 * As such, it can be implemented by simply wrapping Symfony's classes, but can also be implemented
 * with less code and dependencies by just implementing the few required methods outlined in the
 * interface.
 *
 * @see http://api.symfony.com/3.1/Symfony/Component/HttpFoundation/Request.html for insights
 */
interface Request {
  /**
   * Get the designated request parameter, with an optional default value in case the parameter isn't set
   *
   * Parameters can come from the URI query string or the POST body parameters
   *
   * @param string $key -- The parameter key
   * @param string $default -- An optional value to return as default if the parameter isn't set
   * @return string
   */
  function get($key, $default=null);

  /**
   * Get the METHOD of the request (GET, POST, PUT, DELETE....)
   *
   * @return string -- An all-caps string representing the HTTP method of the request
   */
  function getMethod();

  /**
   * Get the preferred human language for the response
   *
   * @return string|null -- The preferred language in which the response should be sent
   */
  function getPreferredLanguage();

  /**
   * Set the method of a Request
   *
   * This is often used in testing to emulate HTTP requests
   *
   * @return void
   */
  function setMethod($method);

  /**
   * Get a Uri representation of the request
   *
   * @return Uri
   */
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
 * A generic interface for Authenticated User management
 */
interface AuthenticatedUser {
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
 * A generic interface for Responses as required by the Skel framework
 *
 * This is inspired by Symfony's HttpFoundation classes, but is intended to be much simpler
 *
 * As such, it can be implemented by simply wrapping Symfony's classes, but can also be implemented
 * with less code and dependencies by just implementing the few required methods outlined in the
 * interface.
 *
 * @see http://api.symfony.com/3.1/Symfony/Component/HttpFoundation/Response.html for insights
 */
interface Response {
  /**
   * Get the content of the response
   *
   * Since a response can contain much more data than just the content, this allows you to obtain
   * just the visible part of the response that the user will see.
   *
   * @return string $content
   */
  function getContent();

  /**
   * Get the status code of the response
   *
   * @return int $code
   */
  function getStatusCode();

  /**
   * Send the response to the client that made the request
   *
   * This should send all headers and echo all content in the response.
   *
   * @return Response $this -- Should return `$this` for possible method chaining
   */
  function send();

  /**
   * Set the user-visible content of the response
   *
   * @param string $content -- The content to send to the client
   * @return Response $this -- Should return `$this` for possible method chaining
   */
  function setContent($content);

  /**
   * Set the HTTP status code of the response
   *
   * @param int $code -- The HTTP status code
   * @param string $text -- An optional string message to briefly explain what happened.
   * @return Response $this -- Should return `$this` for possible method chaining
   */
  function setStatusCode($code, $text=null);
}






/**
 * A generic route interface.
 *
 * This is mostly just a static data structure used in type-checking to enforce presence of expected data.
 */
interface Route {
  /**
   * Run the code associated with this route
   *
   * @param array $vars -- The variables to pass as arguments to the executing code
   * @return Component -- Returns the value returned by the executed code, which should be a Component
   */
  function execute(array $vars);

  /**
   * Get the variables parsed out of the request URI by `match`
   *
   * @return array|null $vars -- The variables parsed out of the URI, if available
   */
  function getMatchedVars();

  /**
   * Get the name of this route, if set
   *
   * @return string|null -- The name of the route or null if not set
   */
  function getName();

  /**
   * Get a usable path for this route, given the provided variables
   *
   * Note: this is like a reverse "match"
   *
   * @param array $vars -- The variables to sub into the path specification to create the completed path 
   * @return string $path
   */
  function getPath(array $vars);

  /**
   * Check to see whether the given request matches this route
   *
   * @param Request $request -- The request object against which to match
   * @return bool
   */
  function match(Request $request);

  /**
   * Set the name of this route
   *
   * @param string $name
   * @return Route $this -- Should return `$this` for method chaining
   */
  function setName(string $name);
}






/**
 * A generic interface for providing routing functionality in an application
 */
interface Router {
  /**
   * Add a route object to this router's collection
   *
   * @param Route $route -- the object to add
   * @return Router $this -- Should return `$this` for method chaining
   */
  function addRoute(Route $route);

  /**
   * Get the usable path of a named route given the provided variables
   *
   * @param string $name -- The name of the route whose path you'd like to get
   * @param array $vars -- An array of variables to sub in for the dynamic parts of the Route
   */
  function getPath($name, $vars);

  /**
   * Routes a request to a handler
   *
   * @param Request $request - the request being routed
   * @return Component -- The result of the handler function, which should be a Component
   */
  function routeRequest(Request $request);

  /**
   * Returns a Route object with the specified name
   *
   * @param string $name
   * @return Route|null -- The Route object with the given name, if any
   */
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
   * instead of an observer/handler pair, I believe using an observer object/handler pair will usually result
   * in better code reuse and more logical design.
   *
   * Also note that the arguments that are sent to the handler cannot be defined by this interface, so each
   * implementation may handle the passing of arguments to handlers different. To me, it makes sense to
   * pass the Observable as the first argument, followed by any arbitrary arguments passed by the application
   * to `notifyListeners`.
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
     */
    interface App extends Context {
      /**
       * Clears a request that has been set for the app
       *
       * This should return the app to a neutral state after setting and then executing on
       * a Request object.
       *
       * @return Request -- The request that was cleared
       */
      function clearRequest();

      /**
       * Generates an error response
       *
       * @param int $code  The error response code
       * @param string $header -- an optional string to set as the "header" element in the Component
       * @param string $text -- an optional string to set as the "text" element in the Component
       * @return Component $errorComponent -- a Component object that can be rendered to a response or
       * further manipulated
       */
      function getError(int $code=404, string $header=null, string $text=null);

      /**
       * Get a Response object, given a request
       *
       * This is more or less the central method when dealing with a standard HTTP app. It
       * is usually called after application setup, and the result is a Response object that
       * can immediately be sent.
       *
       * @param Request $request -- a Request object for which to get a Response
       * @return Response -- a Response object that can be sent to the client using its `send`
       * method
       */
      function getResponse(Request $request=null);

      /**
       * Get the request set by `setRequest`
       *
       * @return Request
       */
      function getRequest();

      /**
       * Get the Router object responsible for routing requests for this app
       *
       * @return Router
       */
      function getRouter();

      /**
       * Get a template object with the given name
       *
       * @param string $name -- The name of the desired template
       * @return Template -- A template object
       */
      function getTemplate(string $name);

      /**
       * Send a `Location: [$url]` header along with a redirect response with the given `$code`
       *
       * This should immediately stop app execution and send the redirect request to the client
       * 
       * @param string $url -- The string URL to redirect to
       * @param int @code -- an optional HTTP redirect code (note: this should default to a sensical value)
       * @return void
       */
      function redirect(string $url, int $code=303);

      /**
       * Set the Request object for the application
       *
       * This allows you to set a Request object that can be referenced from various parts of the
       * app. (This might be used in the event that you want to follow a more specific sequence
       * of execution through the app than `getResponse` allows.)
       *
       * @param Request $request -- the Request object to set for the app
       * @return App $this -- should return `$this` for method chaining
       */
      function setRequest(Request $request);
    }






/**
 * Interface for an object that may produce errors
 */
interface ErrorHandler {
  /**
   * Get the errors that the object has produced, optionally filtering for a specific field
   *
   * @param string|null $field -- An optional field for which to get errors
   * @return array -- A two-dimensional associative array containing arrays of errors indexed by their fields
   * e.g., `array('my-field' => array('error1','error2'))`
   */
  function getErrors(string $field=null);

  /**
   * Get the number of errors this object has produced, optionally filtering for a specific field
   *
   * @param string|null $field -- An optional field for which to count errors
   * @return int -- The number of errors
   */
  function numErrors(string $field=null);
}





/** A generic language interface */
interface Lang {
  /** Get the language code (e.g., 'en', 'es', or 'de') */
  public function getLangCode();

  /** Get the language name (e.g., 'English', 'Español', or 'Deutsch') */
  public function getLangName();
}




