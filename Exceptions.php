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

//TODO: delete this in favor of "InvalidDataException"
class DataValidationException extends \RuntimeException {}

class UnknownFieldException extends \RuntimeException {}
class InvalidDataException extends \RuntimeException {}
class InvalidContentException extends InvalidDataException {}
class DisallowedContentClassException extends InvalidContentException {}
class NonexistentContentClassException extends InvalidContentException {}
class NondescendentContentClassException extends InvalidContentException {}

class UnconvertibleContentException extends \RuntimeException {}

?>
