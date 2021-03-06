<?hh // strict
namespace traitorous\render\views;

use traitorous\render\View;

class JsonView implements View {

    public function __construct(private array<string, string> $_data) { }

    public function render(): string {
        return json_encode($this->_data);
    }

}