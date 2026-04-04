<?php

namespace App\Filament\Pages;

use App\Enums\PermissionScreenEnum;
use App\Models\Permission;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class PermissionPage extends Page implements HasForms
{
    protected string $view = 'filament.pages.permission-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    public ?array $data = [];

    public $permissions;

    use InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill();
        $this->loadPermissions();
        $this->prepareData();
        $this->permissions = $this->permissions->toArray();
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('activateAll')
                ->label(__('Ativar todas as permissões'))
                ->color('success')
                ->size('xl')
                ->visible(fn() => filled($this->data['role']) && filled($this->data['screen']))
                ->icon('heroicon-o-bolt')
                ->requiresConfirmation()
                ->action(fn() => $this->activeAll()),
            Action::make('disableAll')
                ->label(__('Desativar todas as permissões'))
                ->color('danger')
                ->visible(fn() => filled($this->data['role']) && filled($this->data['screen']))
                ->size('xl')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->action(fn() => $this->disableAll()),
            Action::make('save')
                ->label(__('Salvar alterações'))
                ->color('primary')
                ->visible(fn() => filled($this->data['role']) && filled($this->data['screen']))
                ->size('xl')
                ->icon('heroicon-o-check')
                ->action(fn() => $this->save()),
        ];
    }

    private function loadPermissions(): void
    {
        $this->permissions = Permission::select(['id', 'name', 'screen'])
            ->get()
            ->groupBy('screen');
    }

    private function prepareData(): void
    {
        $permissionData = $this
            ->permissions
            ->flatMap(fn($permissionGroup) => $permissionGroup->pluck('name'))
            ->flatMap(fn($name) => [$name => 0])
            ->toArray();

        $this->data = array_merge($this->data, $permissionData);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Fieldset::make(__('Selecione'))
                    ->schema([
                        Select::make('role')
                            ->required()
                            ->afterStateUpdated(fn() => $this->refreshPermissions())
                            ->live()
                            ->searchable()
                            ->options(function () {
                                return Role::pluck('name', 'id')->toArray();
                            })
                            ->allowHtml()
                            ->translateLabel()
                            ->native(false),
                        Select::make('screen')
                            ->required()
                            ->label(__('Tela que receberá as permissões'))
                            ->afterStateUpdated(fn() => $this->updatePermissions())
                            ->live()
                            ->default(PermissionScreenEnum::ROLES_SCREEN->value)
                            ->options(PermissionScreenEnum::options())
                            ->translateLabel()
                            ->selectablePlaceholder(false)
                            ->native(false),
                    ])
            ])
            ->statePath('data');
    }

    private function refreshPermissions(): void
    {
        $this->updatePermissions();
        $this->updateRolePermissions();
    }

    public function permissions(): Schema
    {
        if (blank($this->data['role'])) {
            return new Schema($this);
        }
        $permissions = Arr::get($this->permissions, $this->data['screen']);
        return (new Schema($this))->schema([
            Grid::make(3)->schema(
                array_map(
                    fn($permission) => Fieldset::make('')
                        ->schema([
                            ToggleButtons::make($permission['name'])
                                ->boolean()
                                ->inline()
                                ->grouped(),
                        ]),
                    $permissions
                )
            ),
        ])->statePath('data');
    }

    public function save()
    {
        $this->validate();

        if (blank($this->data['role']) || blank($this->data['screen'])) {
            return;
        }

        $role = Role::find($this->data['role']);

        // Keep permissions from other screens and only replace current screen permissions.
        $currentScreenPermissions = Permission::where('screen', $this->data['screen'])
            ->get(['id', 'name']);

        $selectedCurrentScreenPermissionIds = $currentScreenPermissions
            ->filter(fn (Permission $permission) => (int) ($this->data[$permission->name] ?? 0) === 1)
            ->pluck('id')
            ->toArray();

        $otherScreenPermissionIds = $role->permissions()
            ->where('screen', '!=', $this->data['screen'])
            ->pluck('permissions.id')
            ->toArray();

        $role->permissions()->sync([
            ...$otherScreenPermissionIds,
            ...$selectedCurrentScreenPermissionIds,
        ]);

        Notification::make()
            ->title(__('Permissões atualizadas com sucesso'))
            ->success()
            ->send();

    }

    protected function updatePermissions()
    {
        if (blank($this->data['screen'])) {
            return;
        }

        // 1. Resetar todos os toggles da tela atual
        foreach ($this->getPermissionsOfData() as $permissionKey) {
            $this->data[$permissionKey] = 0;
        }

        // 2. Atualizar com permissões da role para a nova screen
        $this->updateRolePermissions();
    }

    public function activeAll(): void
    {
        $permissionsToActive = Arr::except($this->data, ['role', 'screen']);
        foreach ($permissionsToActive as $key => $value) {
            $this->data[$key] = 1;  // set all as true;
        }

        Notification::make()
            ->title(__('Todas as permissões foram ativadas'))
            ->success()
            ->send();
    }

    public function disableAll(): void
    {
        $permissionsToActive = Arr::except($this->data, ['role', 'screen']);
        foreach ($permissionsToActive as $key => $value) {
            $this->data[$key] = 0;  // set all as true;
        }

        Notification::make()
            ->title(__('Todas as permissões foram desativadas'))
            ->success()
            ->send();
    }

    public function disableAllByCategory(): void
    {
        $categoryPermissions = Permission::where('screen', $this->data['screen'])->get();

        foreach ($this->data as $key => $value) {
            $categoryPermissions->each(function (Permission $permission) use ($key) {
                if ($permission->name == $key) {
                    $this->data[$key] = 0;  // false
                }
            });
        }
        Notification::make()
            ->success()
            ->title(__('All permissions of this category have been disabled'))
            ->send();
    }

    public function activateAllByCategory()
    {
        $categoryPermissions = Permission::where('screen', $this->data['screen'])->get();

        foreach ($this->data as $key => $value) {
            $categoryPermissions->each(function (Permission $permission) use ($key) {
                if ($permission->name == $key) {
                    $this->data[$key] = 1;  // true
                }
            });
        }
        Notification::make()
            ->success()
            ->title(__('All permissions of this category have been activated'))
            ->send();
    }

    protected function updateRolePermissions()
    {
        if (blank($this->data['role']) || blank($this->data['screen'])) {
            return;
        }

        $role = Role::find($this->data['role']);
        $permissions = $role->permissions()->where('screen', $this->data['screen'])->pluck('name')->toArray();

        foreach ($this->getPermissionsOfData() as $permissionKey) {
            $this->data[$permissionKey] = in_array($permissionKey, $permissions) ? 1 : 0;
        }
    }

    public function getPermissionsOfData(): array
    {
        return collect($this->data)
            ->keys()
            ->filter(fn($key) => !in_array($key, ['role', 'screen']))
            ->toArray();
    }

    public function getTitle(): string|Htmlable
    {
        return __('Dar Permissões as Funções');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dar Permissões as Funções');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermission(Permission::where('screen', PermissionScreenEnum::GIVE_PERMISSIONS_TO_ROLES_SCREEN->value)->where('name', 'acessar')->first());
    }
}
