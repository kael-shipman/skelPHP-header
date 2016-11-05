<?php
namespace Skel;

class ApplicationPermissionsException extends \RuntimeException {}
class Http404Exception extends \RuntimeException {}
class UnauthenticatedUserException extends \RuntimeException { }
class UnauthorizedFunctionAccessException extends \RuntimeException { }
class UndefinedActionException extends \RuntimeException {}

class NonexistentFileException extends \RuntimeException {}
class IllegalContentUriException extends \RuntimeException {}

class NonexistentConfigException extends \InvalidArgumentException {}

class DataValidationException extends \RuntimeException {}
class InvalidDataException extends \RuntimeException {}
class InvalidContentException extends InvalidDataException {}
class UnknownContentClassException extends InvalidContentException {}
class UnknownContentFieldException extends UnknownContentClassException {}

class UnconvertibleContentException extends \RuntimeException {}

?>
