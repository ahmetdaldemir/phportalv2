<?php

namespace App\View\Components\ListPage;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public ?string $title;
    public ?string $badge;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title = null, ?string $badge = null)
    {
        $this->title = $title;
        $this->badge = $badge;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.list-page.card');
    }
}
