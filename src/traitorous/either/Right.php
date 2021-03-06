<?hh // strict
namespace traitorous\either;

use traitorous\algebraic\Applicative;
use traitorous\algebraic\Monad;
use traitorous\Either;
use traitorous\outlaw\Eq;
use traitorous\outlaw\Ord;
use traitorous\outlaw\Show;
use traitorous\Option;
use traitorous\option\Some;
use traitorous\Validation;
use traitorous\validation\Success;
use traitorous\validation\Failure;

final class Right<Tl, Tr> implements Either<Tl, Tr> {

    public function __construct(private Tr $_inner) { }

    public function ap<Tb, Tc>(Applicative<Tb> $next): Either<Tl, Tc> {
        // UNSAFE
        return $next->map($this->_inner);
    }

    public function equals(Either<Tl, Tr> $other): bool {
        invariant($this->_inner instanceof Eq, "Expected Left to contain an Eq");
        if ($this->_inner instanceof Eq) {
            return $other->cata(
                ($_) ==> false,
                ($n) ==> $this->_inner->equals($n)
            );
        } else {
            return $other->cata(
                ($_) ==> false,
                ($n) ==> $this->_inner === $n
            );
        }
    }

    public function map<Tb>((function(Tr): Tb) $f): Either<Tl, Tb> {
        return new Right($f($this->_inner));
    }

    public function leftMap<Tb>((function(Tl): Tb) $f): Either<Tb, Tr> {
        // UNSAFE
        return $this;
    }

    public function getEnumKey(): int {
        return Either::RIGHT;
    }

    public function flatMap<Tb>((function(Tr): Monad<Tb>) $f): Either<Tl, Tb> {
        $result = $f($this->_inner);
        invariant($result instanceof Either, "Expected to return an Either<Tl, Tb>");
        return $result;
    }

    public function compare(Either<Tl, Tr> $other): int {
        invariant($this->_inner instanceof Ord, "Expected Left to contain an Eq");
        return $other->cata(
            ($_) ==> Ord::GREATER,
            ($n) ==> $this->_inner->compare($n)
        );
    }

    public function invert(): Either<Tr, Tl> {
        return new Left($this->_inner);
    }

    public function toOption(): Option<Tr> {
        return new Some($this->_inner);
    }

    public function toSuccess<Ta>((function():Ta) $f): Validation<Ta, Tr> {
        return new Success($this->_inner);
    }

    public function toFailure<Ta>((function():Ta) $s): Validation<Tr, Ta> {
        return new Failure($this->_inner);
    }

    public function getOrElse((function(): Tr) $f): Tr {
        return $this->_inner;
    }

    public function getOrDefault(Tr $default): Tr {
        return $this->_inner;
    }

    public function show(): string {
        invariant($this->_inner instanceof Show, "Expected either to contain a Show");
        $inner = $this->_inner->show();
        invariant(is_string($inner), "Expected a string");
        return "Right({$inner})";
    }

    public function cata<Tb>((function(Tl): Tb) $left, (function(Tr): Tb) $right): Tb {
        return $right($this->_inner);
    }

}