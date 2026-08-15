<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trazer um item para um residente</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #b45309;
            --primary-dark: #92400e;
            --ink: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #fdfaf5;
            --success-bg: #ecfdf5;
            --success-border: #34d399;
            --success-text: #065f46;
            --error-bg: #fef2f2;
            --error-border: #f87171;
            --error-text: #991b1b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--ink);
            padding: 1.5rem;
            min-height: 100vh;
        }
        .card {
            max-width: 40rem;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }
        h1 {
            font-size: 1.5rem;
            margin: 0 0 0.25rem;
        }
        p.lead {
            color: var(--muted);
            margin: 0 0 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }
        .alert {
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border: 1px solid;
        }
        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }
        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error-text);
        }
        .alert-error ul {
            margin: 0.25rem 0 0;
            padding-left: 1.25rem;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.35rem;
            font-size: 0.95rem;
        }
        .field {
            margin-bottom: 1.25rem;
        }
        .hint {
            color: var(--muted);
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }
        input[type="text"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            font-size: 1.05rem;
            padding: 0.7rem 0.85rem;
            border: 1px solid var(--border);
            border-radius: 0.6rem;
            background: #fff;
            color: var(--ink);
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }
        textarea { resize: vertical; min-height: 4.5rem; }
        button {
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 0.9rem 1rem;
            border: none;
            border-radius: 0.6rem;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
        }
        button:hover { background: var(--primary-dark); }
        .row-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 32rem) {
            .row-2 { grid-template-columns: 1fr 1fr; }
        }
        .combobox {
            position: relative;
        }
        .combobox-list {
            list-style: none;
            margin: 0.35rem 0 0;
            padding: 0.35rem;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 20;
            max-height: 15rem;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 0.6rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .combobox-list li {
            padding: 0.6rem 0.7rem;
            border-radius: 0.4rem;
            cursor: pointer;
            font-size: 1rem;
        }
        .combobox-list li.active,
        .combobox-list li:hover {
            background: #fef3e2;
            color: var(--primary-dark);
        }
        .combobox-list li.empty {
            color: var(--muted);
            cursor: default;
        }
        .combobox-list li.empty:hover {
            background: transparent;
        }
        .combobox.has-error input[type="text"] {
            border-color: var(--error-border);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Trazer um item para um residente</h1>
        <p class="lead">Preencha os dados abaixo para registrar um item (ex.: travesseiro, edredom) para um residente específico. Nossa equipe confere e confirma o recebimento na recepção.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Não foi possível registrar:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-donations.store') }}">
            @csrf

            <div class="field">
                <label for="patient_document_number">CPF do residente</label>
                <input
                    type="text"
                    id="patient_document_number"
                    name="patient_document_number"
                    inputmode="numeric"
                    placeholder="Ex.: 123.456.789-00"
                    value="{{ old('patient_document_number') }}"
                    required
                >
                <p class="hint">Não sabe o CPF? Pergunte na recepção antes de continuar.</p>
            </div>

            @php
                $selectedStockItem = $stockItems->firstWhere('id', old('stock_item_id'));
            @endphp
            <div class="field combobox" data-combobox>
                <label for="stock_item_search">O que você está trazendo?</label>
                <input
                    type="text"
                    id="stock_item_search"
                    autocomplete="off"
                    role="combobox"
                    aria-expanded="false"
                    aria-controls="stock_item_list"
                    placeholder="Digite para buscar (ex.: travesseiro)"
                    value="{{ $selectedStockItem?->name }}"
                    data-combobox-input
                    required
                >
                <input
                    type="hidden"
                    name="stock_item_id"
                    id="stock_item_id"
                    value="{{ old('stock_item_id') }}"
                    data-combobox-value
                >
                <ul id="stock_item_list" class="combobox-list" role="listbox" data-combobox-list hidden></ul>
                <p class="hint" data-combobox-hint>Comece a digitar e escolha um item da lista.</p>
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="quantity">Quantidade</label>
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        min="1"
                        value="{{ old('quantity', 1) }}"
                        required
                    >
                </div>

                <div class="field">
                    <label for="donor_phone">Seu telefone (opcional)</label>
                    <input
                        type="tel"
                        id="donor_phone"
                        name="donor_phone"
                        placeholder="(11) 91234-5678"
                        value="{{ old('donor_phone') }}"
                    >
                </div>
            </div>

            <div class="field">
                <label for="donor_name">Seu nome</label>
                <input
                    type="text"
                    id="donor_name"
                    name="donor_name"
                    placeholder="Como podemos te chamar na recepção"
                    value="{{ old('donor_name') }}"
                    required
                >
            </div>

            <div class="field">
                <label for="notes">Mensagem (opcional)</label>
                <textarea id="notes" name="notes" placeholder="Alguma observação sobre o item...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit">Registrar item</button>
        </form>
    </div>

    @php
        $stockItemsForJs = $stockItems->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => $item->name,
                'detail' => trim($item->current_quantity . ' ' . strtolower($item->unit->value) . ' em estoque'),
            ];
        });
    @endphp
    <script>
        (function () {
            var stockItems = @json($stockItemsForJs);

            var wrapper = document.querySelector('[data-combobox]');
            var input = wrapper.querySelector('[data-combobox-input]');
            var hidden = wrapper.querySelector('[data-combobox-value]');
            var list = wrapper.querySelector('[data-combobox-list]');
            var hint = wrapper.querySelector('[data-combobox-hint]');
            var defaultHint = hint.textContent;
            var activeIndex = -1;
            var filtered = [];

            function normalize(value) {
                return value
                    .normalize('NFD')
                    .replace(/\p{Diacritic}/gu, '')
                    .toLowerCase();
            }

            function clearError() {
                wrapper.classList.remove('has-error');
                hint.textContent = defaultHint;
            }

            function render() {
                list.innerHTML = '';

                if (filtered.length === 0) {
                    var empty = document.createElement('li');
                    empty.className = 'empty';
                    empty.textContent = input.value.trim()
                        ? 'Nenhum item encontrado.'
                        : 'Comece a digitar para ver os itens.';
                    list.appendChild(empty);
                } else {
                    filtered.forEach(function (item, index) {
                        var li = document.createElement('li');
                        li.setAttribute('role', 'option');
                        li.textContent = item.label + (item.detail ? ' — ' + item.detail : '');
                        if (index === activeIndex) {
                            li.classList.add('active');
                        }
                        li.addEventListener('mousedown', function (event) {
                            event.preventDefault();
                            select(item);
                        });
                        list.appendChild(li);
                    });
                }

                list.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            }

            function close() {
                list.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            }

            function select(item) {
                input.value = item.label;
                hidden.value = item.id;
                clearError();
                close();
            }

            function search(term) {
                var query = normalize(term.trim());
                filtered = query
                    ? stockItems.filter(function (item) {
                        return normalize(item.label).includes(query);
                    }).slice(0, 30)
                    : stockItems.slice(0, 30);
                activeIndex = -1;
                render();
            }

            input.addEventListener('input', function () {
                hidden.value = '';
                clearError();
                search(input.value);
            });

            input.addEventListener('focus', function () {
                search(input.value);
            });

            input.addEventListener('keydown', function (event) {
                if (list.hidden && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
                    search(input.value);
                    return;
                }

                if (list.hidden) return;

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, filtered.length - 1);
                    render();
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    render();
                } else if (event.key === 'Enter') {
                    if (activeIndex >= 0 && filtered[activeIndex]) {
                        event.preventDefault();
                        select(filtered[activeIndex]);
                    }
                } else if (event.key === 'Escape') {
                    close();
                }
            });

            document.addEventListener('click', function (event) {
                if (!wrapper.contains(event.target)) {
                    close();
                }
            });

            wrapper.closest('form').addEventListener('submit', function (event) {
                if (!hidden.value) {
                    event.preventDefault();
                    wrapper.classList.add('has-error');
                    hint.textContent = 'Selecione um item da lista antes de continuar.';
                    input.focus();
                }
            });
        })();
    </script>
</body>
</html>
