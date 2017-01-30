<?php
namespace Skel;

// Application exceptions
/** Application can't find the execution path the user is looking for */
class Http404Exception extends \RuntimeException {}
/** Controller has returned an invalid value (should always return a `Component` */
class InvalidControllerReturnException extends \RuntimeException {}
/** Something has requested that the App stop executing */
class StopAppException extends \RuntimeException {}


// Exceptions having to do with object states
/** Object hasn't been given adequate properties to function correctly (e.g., no Template set on Component) */
class UnpreparedObjectException extends \RuntimeException {}


// Filesystem exceptions
/** A file that was specified at a certain FS url is not there */
class NonexistentFileException extends \RuntimeException {}


// Configuration exceptions
/** The requested configuration key doesn't exist (this is to tell you to set it) */
class NonexistentConfigException extends \InvalidArgumentException {}
/** The config key exists, but isn't returning the type of value expected */
class InvalidConfigException extends \InvalidArgumentException {}


// Content Exceptions (for CMS)
/**
 * The CMS or related object doesn't know how to handle a given field
 *
 * This will often be thrown when field names are used to convert values between "raw" and "user-facing" states.
 * For example one might pass `dateCreated` to a function in the CMS, and if the CMS doesn't know what the field
 * `dateCreated` is supposed to contain, it will throw this exception.
 */
class UnknownFieldException extends \RuntimeException {}
/** A specific field in an object is invalid */
class InvalidDataFieldException extends \RuntimeException {}
/** A whole object is invalid, usually in the context of a collection of sibling objects */
class InvalidDataObjectException extends InvalidDataFieldException {}
/** The system doesn't know how to conver the given serialized content into a program Object via its `contentClass` field */
class UnknownContentClassException extends InvalidDataFieldException {}


// Content Conversion
/** The given ContentConverter doesn't know how to convert the given content */
class UnconvertibleContentException extends \RuntimeException {}


// Database exceptions
/** An object found that the current database schema does not fulfill its basic requirements */
class InadequateDatabaseSchemaException extends \RuntimeException {}
/** The system doesn't know how to save the given associated collection (this is for m2m and o2m relationships in a CMS) */
class UnsaveableAssociatedCollectionException extends \RuntimeException {}


// ContentSync exceptions
/** The given ContentFile has validity issues (perhaps the format is wrong, for example, or it's missing certain headers */
class InvalidContentFileException extends \RuntimeException {}

