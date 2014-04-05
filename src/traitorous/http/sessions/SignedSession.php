<?hh // strict
namespace traitorous\http\sessions;

use traitorous\http\HttpRequest;
use traitorous\http\Session;

final class SignedSession extends Session {

    public function cata<T>((function(): T) $s, (function(): T) $f): \T {
        return $s();
    }

    public static function fromRequest(string $secret, HttpRequest $request): SignedSession {
        return new SignedSession($secret, Session::fromRequest("session", $secret, $request));
    }

}