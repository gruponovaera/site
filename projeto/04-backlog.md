# Backlog Global (Shared)

## Identidade e UI
- [ ] Definir tokens de cor e tipografia para o site
- [ ] Consolidar estilos inline do `index.html`
- [ ] Unificar visual do admin com o site ou manter isolado
- [ ] Criar ativos visuais: Logo em SVG, Placas promocionais, Banners para redes sociais

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
- [ ] Padronizar assets: converter fotos para **WebP** e ilustracoes/logos para **SVG**
- [ ] Avaliar uso de Tailwind CDN vs build local
- [ ] Revisar fontes externas (Google Fonts)

## Admin/Participantes
- [ ] Implementar **Auth Basic (.htaccess)** como protecao inicial para `participantes/`
- [ ] Planejar e implementar evolucao para **Login PHP** com sessao
- [ ] Exportar lista em CSV
- [ ] Totalizadores por reuniao
- [ ] Escape de saida para nomes/e-mails

## Webhook/Zoom
- [ ] Validar assinatura/secret do Zoom
- [ ] Validar tipo de evento antes de inserir
- [ ] Criar arquivo `env.example` com placeholders para segredos (DB, Zoom)
- [ ] Remover tokens e credenciais do codigo e usar variaveis de ambiente

## Banco de Dados
- [ ] Criar indice em `join_time`
- [ ] Avaliar indice em `email`
- [x] Definir e documentar timezone padrao (`America/Sao_Paulo`)

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

---

## checklist de QA (Quality Assurance)
- [ ] **Funcional:** Os links do menu (`navigateTo`) funcionam corretamente?
- [ ] **Responsividade:** O site e legivel em Mobile (320px), Tablet e Desktop?
- [ ] **Acessibilidade:** O contraste das cores permite a leitura clara? (Alvo: AA)
- [ ] **Performance:** As imagens estao em WebP e abaixo de 200kb?
- [ ] **Seguranca:** O acesso a `/participantes/` solicita credenciais?
- [ ] **Dados:** As presencas gravadas estao com o timezone `America/Sao_Paulo`?
- [ ] **Integridade:** Nao existem links quebrados ou placeholders (???) no texto?
