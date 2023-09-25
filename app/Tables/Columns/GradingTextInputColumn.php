<?php

namespace App\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Concerns\CanBeValidated;
use Filament\Tables\Columns\Concerns\CanUpdateState;
use Filament\Tables\Columns\Column;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Forms\Components\Concerns\HasInputMode;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;

class GradingTextInputColumn extends Column implements Editable
{
    use CanBeValidated;
    use CanUpdateState;
    use HasExtraInputAttributes;
    use HasInputMode;
    use HasPlaceholder;
    use HasStep;

    protected string $view = 'tables.columns.grading-text-input-column';

    protected string | Closure | null $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type) ?? 'text';
    }

}
