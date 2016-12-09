<?php
namespace Skel;

class ApplicationPermissionsException extends \RuntimeException {}
class Http404Exception extends \RuntimeException {}
class UnauthenticatedUserException extends \RuntimeException { }
class UnauthorizedFunctionAccessException extends \RuntimeException { }
class UndefinedActionException extends \RuntimeException {}
class InvalidControllerReturnException extends \RuntimeException {}
class StopAppException extends \RuntimeException {}

class NonexistentFileException extends \RuntimeException {}
class IllegalContentUriException extends \RuntimeException {}

class NonexistentConfigException extends \InvalidArgumentException {}

class UnknownFieldException extends \RuntimeException {}
class InvalidDataFieldException extends \RuntimeException {}
class InvalidDataObjectException extends InvalidDataFieldException {}

class UnconvertibleContentException extends \RuntimeException {}

class InadequateDatabaseSchemaException extends \RuntimeException {}
class UnsaveableAssociatedCollectionException extends \RuntimeException {}

class InvalidContentFileException extends \RuntimeException {}

?>
