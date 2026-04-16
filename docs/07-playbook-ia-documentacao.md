# Playbook de IA para Documentacao

## Objetivo
Padronizar como usar IA para manter a documentacao consistente e aderente ao codigo real.

## Prompt Base para Fluxo
```text
Analise o fluxo abaixo no projeto Laravel e documente em portugues tecnico.
Formato obrigatorio:
1) Objetivo do fluxo
2) Endpoints e entradas
3) Arquivos envolvidos e responsabilidade
4) Sequencia detalhada (Controller -> DTO -> Service -> Model -> Resource)
5) Regras de negocio explicitas e implicitas
6) Regras de autorizacao e validacao
7) Erros esperados e codigos HTTP
8) Riscos e edge cases
9) Cenarios de teste recomendados

Nao invente comportamento. Se nao estiver claro no codigo, marque como "A confirmar".
```

## Prompt para Atualizacao de Doc em PR
```text
Com base no diff do PR, atualize os arquivos em docs/ impactados.
Inclua:
- o que mudou na regra de negocio
- impacto em endpoints e validacoes
- impacto em autorizacao/policies
- novos edge cases
Se nao houver impacto funcional, registrar explicitamente "Sem mudanca de regra de negocio".
```

## Prompt para Diagrama Mermaid
```text
Gere um diagrama Mermaid sequenceDiagram para o fluxo X.
Inclua validacao, autorizacao, transacao, persistencia e resposta HTTP.
Use nomes reais de classes do projeto quando existirem.
```

## Checklist de Qualidade para IA
- O texto cita arquivos reais do projeto.
- As regras refletem validacoes de FormRequest ou validate().
- O fluxo respeita transacoes existentes.
- Nao cria regras que nao existem.
- Destaca pontos "A confirmar" quando faltar evidencia.
