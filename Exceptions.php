<?php
namespace Skel;

class UnauthenticatedUserException extends \RuntimeException { }
class UnauthorizedFunctionAccessException extends \RuntimeException { }
class UndefinedActionException extends \RuntimeException {}
class NonexistentFileException extends \RuntimeException {}
class IllegalContentUriException extends \RuntimeException {}

class InvalidDataException extends \RuntimeException {}
class InvalidContentException extends InvalidDataException {}
class DisallowedContentClassException extends InvalidContentException {}
class NonexistentContentClassException extends InvalidContentException {}
class NondescendentContentClassException extends InvalidContentException {}

?>
