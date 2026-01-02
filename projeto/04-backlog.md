# Backlog Global (Shared)

## Identidade e UI
- [ ] Definir tokens de cor e tipografia para o site
- [ ] Consolidar estilos inline do `index.html`
- [ ] Unificar visual do admin com o site ou manter isolado

## Home / Single-page
- [ ] Corrigir acentuacao/encoding do texto do `index.html`
- [ ] Substituir placeholders e icones faltantes
- [ ] Validar horarios, ID e senha das reunioes
- [ ] Garantir fallback quando JS estiver desativado

## Conteudo e SEO
- [ ] Revisar meta description e title conforme conteudo final
- [ ] Revisar hierarquia de headings por secao
- [ ] Completar textos institucionais

## Assets e Dependencias
- [ ] Migrar logo e background para `img/`
- [ ] Avaliar uso de Tailwind CDN vs build local
- [ ] Revisar fontes externas (Google Fonts)

## Admin/Participantes
- [ ] Proteger acesso a `participantes/` (senha/HTTP Basic)
- [ ] Exportar lista em CSV
- [ ] Totalizadores por reuniao
- [ ] Escape de saida para nomes/e-mails

## Webhook/Zoom
- [ ] Validar assinatura/secret do Zoom
- [ ] Validar tipo de evento antes de inserir
- [ ] Remover tokens do repositorio e usar variaveis de ambiente

## Banco de Dados
- [ ] Criar indice em `join_time`
- [ ] Avaliar indice em `email`
- [ ] Definir e documentar timezone padrao

## SEO e Acessibilidade
- [ ] Texto alternativo nas imagens
- [ ] Foco visivel e contraste AA

## Performance
- [ ] Otimizar imagens em `img/`
- [ ] Reduzir dependencias externas

## Legal e Privacidade
- [x] Licenca publicada em `LICENSE.md`
- [ ] Politica de privacidade (LGPD) em pagina dedicada

## Implementado
- Webhook Zoom com gravacao de presencas
- Pagina de consulta por data/horario
- Pagina institucional inicial em `index.html`
