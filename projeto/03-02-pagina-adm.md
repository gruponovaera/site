# Pagina de Administracao (Participantes)

## Objetivo
- Permitir consulta de presencas registradas pelo webhook do Zoom.

## Escopo
### Inclui
- Formulario de filtro por data e horario
- Lista de presencas com deduplicacao basica
- Base visual simples com `estilo.css`
- Possivel exportacao e totalizadores

### Nao inclui
- Cadastro manual de presencas
- Edicao direta do banco de dados

## Tarefas
### Backend
- [ ] Validar e sanitizar entradas (data e horarios)
- [ ] Padronizar timezone (definir regra no `02-arquitetura.md`)
- [ ] Ajustar deduplicacao para casos com email vazio
- [ ] Adicionar indice em `join_time`
- [ ] Proteger o acesso a `participantes/`
- [ ] Escapar saida para evitar XSS
- [ ] Tratar erros de banco com mensagens claras

### Frontend
- [ ] Revisar layout do formulario e lista
- [ ] Padronizar com o Base Layout ou manter visual separado
- [ ] Exibir totalizadores (ex: total de presencas)

### Operacao
- [ ] Exportar CSV da consulta
- [ ] Registrar logs de consulta (opcional)

## Criterios de Aceitacao (DoD)
- Consulta retorna dados corretos no intervalo informado
- Acesso restrito a administracao
- Saida segura e legivel

## Implementado
- [x] Formulario e lista basica em `participantes/index.php`
