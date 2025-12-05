<?php

namespace App\View\Components\ListPage;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public string $title;
    public ?string $createRoute;
    public ?int $count;
    public ?string $icon;
    public ?string $description;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title,
        ?string $createRoute = null,
        ?int $count = null,
        ?string $icon = 'bx-list-ul',
        ?string $description = null
    ) {
        $this->title = $title;
        $this->createRoute = $createRoute;
        $this->count = $count;
        $this->icon = $icon;
        $this->description = $description ?? $title . ' yönetimi ve düzenleme';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.list-page.header');
    }
}
