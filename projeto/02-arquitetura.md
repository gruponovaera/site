# Arquitetura do Banco de Dados

## Visao geral
- **Banco:** `novaera_participantes`
- **Objetivo:** registrar presencas recebidas do webhook do Zoom
- **Fluxo:** `webhook-zoom/index.php` insere dados; `participantes/index.php` consulta por intervalo
- **Observacao:** a deduplicacao e feita na exibicao, nao no banco

## Tabelas

### novaera_participantes

| Campo | Tipo | Nulo | Descricao |
| --- | --- | --- | --- |
| `id` | INT AUTO_INCREMENT | Nao | Identificador unico |
| `user_name` | VARCHAR(255) | Nao | Nome exibido pelo participante |
| `email` | VARCHAR(255) | Sim | Email do participante (pode ser nulo) |
| `join_time` | DATETIME | Nao | Momento de entrada na reuniao |
| `start_time` | DATETIME | Nao | Inicio da reuniao |

## Indices e chaves
- **PK:** `id`
- **Recomendado:** indice em `join_time` para consultas por intervalo
- **Opcional:** indice em `email` e/ou `(user_name, join_time)` para auditoria

## Regras de dados
- `email` pode ser nulo.
- `join_time` e `start_time` devem seguir o mesmo timezone (definir padrao).
- Os valores vem do Zoom e sao gravados como `DATETIME`.

## Integracao com o sistema
- **Insercao:** `webhook-zoom/index.php`
- **Consulta:** `participantes/index.php`
