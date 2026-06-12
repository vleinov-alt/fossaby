# Fossa-IT Website Redesign — Instructions for Claude (Design Brief)

> Paste this brief into Claude when generating the new fossa.by website. Build pages in the order listed. All visible content is in Russian (an EN toggle exists but RU is primary). Replace placeholder content marked `[TODO]` with real assets before launch.

## 1. Context

Fossa-IT (fossa.by) is a Belarusian IT company. **Key positioning: full-cycle partner — Fossa-IT works at every stage of a client's digitalization**, from their first website to process automation and ongoing support. Three offers:
1. **Development** — websites and landing pages built turnkey, plus simple custom systems and applications: lightweight record-keeping systems, client portals / personal accounts, bots, small web and mobile apps. This is the low-barrier entry point: a client can start with a website or a simple app and grow into automation.
2. **Automation Services** — business process automation, ITSM/CRM implementation, custom workflow apps, low-code, technical support. Audience: owners/COOs of service businesses (10–100 staff) and IT/Operations managers (50–500 staff).
3. **FossaMed** — a vertical SaaS product: medical information system (MIS) for private clinics. Online booking, doctor appointment workflow (ICD-10 codes), unified patient history, document management. Hosted in certified, secured infrastructure inside Belarus. Audience: private clinic owners and operations managers.

Core problem of the current site: two competing H1s on the homepage, mixed funnels, no proof (no screenshots, no metrics in cases), mixed RU/EN strings, thin one-page structure. The redesign must split the two offers into two clear funnels and add proof layers.

## 2. Site map (build exactly this)

```
/                       Home — router page: choose Automation or FossaMed
/automation             Automation services landing (full funnel)
/automation/itsm-crm    ITSM / CRM / service workflows service page
/automation/custom      Custom workflow apps & low-code page
/development            Websites & simple systems/apps landing (entry-level funnel)
/cases                  Case hub (cards) 
/cases/{slug}           Case detail template (gym, auto-service, beauty-salon, restaurant)
/fossamed               FossaMed product landing (full funnel)
/fossamed/features      Feature detail: booking, appointments, patient history, documents
/fossamed/security      Security & compliance page
/fossamed/demo          Demo request page
/contacts               Contacts
/privacy-policy, /terms Keep existing legal pages
```

Navigation: logo → home; top nav = Услуги (dropdown: Автоматизация, Разработка сайтов и приложений, FossaMed), Кейсы, Контакты; persistent CTA button in header — context-aware: «Заказать аудит» on automation pages, «Получить демо» on FossaMed pages, «Получить оценку» on /development, «Связаться» elsewhere.

## 3. Design system (no existing brandbook — define a new one)

- **Logo (mandatory):** the existing Fossa-IT logo is the basis of the identity — do NOT redesign or replace it. Use it as-is in header/footer/favicon and derive the accent palette and overall visual character from its colors and shapes. Any refinement is limited to cleanup (spacing, vector quality, mono/inverted variants for dark and light backgrounds).
- **Tone:** clean, engineering, trustworthy. Closer to a product company than an agency. No stock-photo clichés (handshakes, smiling call centers).
- **Colors:** derive the primary accent from the existing logo. Base: dark slate/near-black (#101418) + white surfaces. FossaMed sub-brand may use a calmer medical tint of the same accent. Keep total palette ≤ 4 colors + grays.
- **Typography:** modern grotesque with full Cyrillic support (Inter, Manrope, or Onest). Large hero headings (clamp 32–56px), 16–18px body, generous line-height.
- **Layout:** 12-col grid, max-width 1200px, lots of whitespace, cards with 12–16px radius, subtle borders instead of heavy shadows.
- **Imagery:** product screenshots in browser/device frames (FossaMed), simple line-style diagrams for processes, abstract geometric accents. Icons: one consistent outline set (e.g., Lucide).
- **Motion:** minimal — fade/slide on scroll, no carousels that auto-rotate. Replace current Previous/Next sliders with static grids.
- **Accessibility:** WCAG AA contrast, visible focus states, all form labels real `<label>` elements, semantic HTML, exactly one H1 per page.
- **Performance:** static-first, no heavy JS frameworks required; WebP images, lazy loading; mobile-first responsive.

## 4. Page-by-page requirements

### / (Home — router)
- Single H1: «IT-партнёр на всех этапах: от сайта до автоматизации».
- Immediately below: three equal route cards:
  - **Автоматизация бизнес-процессов** — 1 line of value, 3 bullet outcomes, CTA «Подробнее» → /automation
  - **Сайты и приложения** — websites, simple systems, client portals, bots; CTA «Подробнее» → /development
  - **FossaMed — система для медицинских центров** — 1 line of value, 3 bullet outcomes, CTA «Подробнее» → /fossamed
- A short «ladder» strip under the cards: начните с малого (сайт или простое приложение) → вырастайте в автоматизацию → мы сопровождаем на каждом этапе.
- Then: trust strip (years, projects, [TODO: client logos]), 3 featured case cards → /cases, short «О компании» block, contact CTA.
- No service detail on the homepage. Its only job is routing + trust.

### /automation
Funnel order: promise → proof → offer → conversion.
1. Hero: H1 «Автоматизация заявок, сервисных и клиентских процессов», subhead about removing manual routine without heavyweight enterprise rollouts. Primary CTA «Заказать аудит процессов», secondary «Написать в Telegram».
2. «Что автоматизируем» — 4–6 outcome cards (заявки и SLA, CRM/клиентские процессы, ITSM, кастомные workflow-приложения, поддержка).
3. «Как мы работаем» — 4-step horizontal timeline: аудит → пилот (30–45 дней) → внедрение → поддержка.
4. Proof: 3 case cards with metrics [TODO: real numbers] → /cases.
5. FAQ accordion (sроки, стек, что входит в аудит, поддержка после запуска).
6. Conversion block: form (имя, телефон, email, что хотите автоматизировать) + Telegram alternative.

### /automation/itsm-crm and /automation/custom
Standard service-page template: H1 matching search intent, who it's for, scope list, implementation timeline, related case, FAQ, same conversion block.

### /development (Websites & simple systems)
Entry-level funnel, friendly to non-technical small-business owners:
1. Hero: H1 «Сайты, простые системы и приложения для бизнеса», subhead: сделаем под ключ — сайт, личный кабинет, учётную систему или бота; срок и цена понятны до старта. CTA «Получить оценку».
2. «Что делаем» — 4 cards: сайты и лендинги; простые учётные системы; личные кабинеты и порталы; боты и небольшие приложения.
3. «Как это работает» — 3 steps: короткий бриф → фиксированная оценка (срок + цена) → запуск и поддержка.
4. Ladder block: «Это только начало» — explain growth path into process automation (link to /automation).
5. 1–2 case cards (e.g., gym app) → /cases.
6. Conversion: short form (имя, контакт, что нужно сделать) + Telegram.

### /fossamed
1. Hero: H1 «FossaMed — медицинская информационная система для частных клиник», subhead: онлайн-запись, ведение приёма, единая история пациента, данные в защищённой инфраструктуре в Беларуси. CTA «Получить демо». Hero visual: product screenshot in a laptop frame [TODO: real screenshot].
2. Feature grid (4): Онлайн-запись, Ведение приёма (МКБ-10), История пациента, Документы и роли — each with a small UI screenshot [TODO].
3. «Как проходит внедрение» — 30/60/90-day plan with what the clinic does vs what Fossa-IT does.
4. Security teaser block (encryption, role access, audits, backups, RB hosting) → link to /fossamed/security.
5. «Что входит в демо» — 3 bullets, removes uncertainty.
6. FAQ (цена/модель оплаты, миграция данных, обучение персонала, поддержка).
7. Demo form (клиника, имя, телефон, email) + Telegram bot button.

### /fossamed/security
Full page: hosting location and certification, encryption at rest/in transit, role-based access, backup policy, RB personal-data-law compliance, security audits. Sober, factual tone; no marketing fluff. This page closes the clinic's main objection.

### /cases and /cases/{slug}
Hub: filterable card grid (отрасль). Detail template: client context → task → solution → stack → timeline → 2–3 result metrics [TODO] → quote → CTA to the matching funnel.

### /contacts
RU-language everything (no «Contacts»/«Submit» strings). Phones BY + RU, email, Telegram, form, requisites, [TODO: map/address if public].

## 5. Forms & analytics (must be wired in)

- All form strings, placeholders, validation and success messages in Russian on RU pages.
- One Yandex.Metrika counter site-wide; goals: form submit, phone click, Telegram click, bot start.
- CTAs to Telegram carry UTM parameters.
- No analytics tags on any future patient-data areas (FossaMed product itself) — marketing pages only.

## 6. SEO requirements

- One H1 per page matching the target query (e.g., /fossamed → «МИС для частных клиник» semantics).
- Unique title/description per page; geo in titles limited to Belarus until other markets have local proof.
- Semantic HTML5 (header/nav/main/section/footer), schema.org Organization + Product (FossaMed) + FAQPage markup.
- Internal linking: every case links to its funnel; every service page links to ≥1 case.

## 7. What NOT to do

- No auto-rotating carousels, no duplicated slide blocks.
- No mixed-language UI on a single page.
- No generic claims without proof («лидеры рынка», «команда экспертов» without facts).
- Don't route paid/medical-intent traffic to the homepage — landings only.
- Don't reuse the same three card images for different services (current site does this).
