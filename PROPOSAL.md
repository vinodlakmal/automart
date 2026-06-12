# Project Proposal — Multi-Category Marketplace Website

**Prepared for:** [Client Name]
**Prepared by:** Vinod Lakmal — Merkei Solutions
**Date:** 12 June 2026
**Project:** Ikman-style free classified-ads marketplace

> Note for you (Vinod): figures below are **starting estimates** — adjust the rate
> table to your own pricing before sending. Everything is in LKR.

---

## 1. Project overview

A web platform where users freely post advertisements to **buy, sell, or rent** across
multiple categories — **Real Estate, Vehicles, Education, Shopping**, plus **Jobs,
Services and Electronics**. The homepage presents each category in clear sections, with
a site-wide search and filter system for easy browsing. Posting is free for users;
premium/paid listings and online payments are planned for a later phase.

## 2. What I need from you to start

To keep the build accurate and on schedule, please provide:

1. **Brand assets** — logo, preferred colours, and any tagline. (If none yet, I can use a clean default theme.)
2. **Design direction** — either a reference site you like (e.g. ikman.lk, riyasewana.com) or a simple sketch/wireframe. A formal design is *not* required to start.
3. **Category list** — confirm the top-level categories and their sub-categories (a draft is already built and seeded).
4. **Content** — homepage banner text, "About"/"Contact" details, and contact phone/email.
5. **Domain & hosting** — domain name and server access (a Linux VPS is already in use).
6. **Policies** — posting rules, prohibited items, and terms & privacy text (I can draft templates).
7. **Phase-2 decisions (later)** — payment provider preference for premium listings (e.g. PayHere, Stripe), and pricing for paid features.

## 3. Features

### Phase 1 — Core marketplace (free posting) — *largely built*

| Area | Features |
|------|----------|
| Listings | Free ad posting with up to 8 images (drag & drop), title, description, price + "negotiable" toggle, condition (new/used), contact details |
| Categories | 7 top categories with sub-categories; category-specific fields (e.g. vehicles: brand/year/mileage; real estate: bedrooms/land size) |
| Location | Sri Lanka district → city cascading selection |
| Browsing | Homepage with per-category sections, keyword search (full-text), filters by category, district, price range and condition |
| Ad page | Image gallery, seller info & contact, related ads, view counter |
| User | Registration/login, "My Ads" dashboard, edit/delete own ads, favourites |
| Moderation | Report-ad function, owner-only edit/delete, soft-delete with image cleanup |
| Mobile | Fully responsive layout |

### Phase 2 — Monetisation & growth — *future*

| Area | Features |
|------|----------|
| Premium listings | Bump-up, "Top ad", "Urgent", and Featured placements (data model already in place) |
| Payments | Online payment gateway integration (PayHere / Stripe) for paid features |
| Messaging | In-app buyer ↔ seller chat (schema already in place) |
| Admin | Admin panel: approve/reject ads, manage categories, view reports, analytics |
| Notifications | Email/SMS alerts for messages and ad status |
| SEO | Search-engine-friendly URLs, sitemaps, meta tags |

## 4. Technology stack

| Layer | Choice | Why |
|-------|--------|-----|
| Backend | **Laravel 12 (PHP 8.2)** | Mature, secure, fast to build on; ideal for marketplaces |
| Frontend | Blade + Tailwind CSS, vanilla JS | Lightweight, responsive, easy to maintain |
| Database | **MySQL 8** | Reliable, supports full-text search for listings |
| Server | Ubuntu VPS + **Nginx**, deployed via **Docker** | Isolated, reproducible, runs alongside other sites safely |
| Source control | Git + GitHub | Versioned code, one-command deploys |

## 5. Timeline

Assuming Phase 1 (core marketplace) — much of which is already implemented:

| Milestone | Work | Duration |
|-----------|------|----------|
| 1. Setup & design | Branding, theme, category finalisation | 1 week |
| 2. Core build | Listings, categories, search/filter, user accounts | 2–3 weeks |
| 3. Polish & content | Responsive QA, pages, testing, bug fixes | 1 week |
| 4. Launch | Deployment, domain, go-live | 2–3 days |
| **Phase 1 total** | | **~4–5 weeks** |
| Phase 2 (premium + payments + admin) | Optional, scoped later | +3–5 weeks |

## 6. Cost estimate (LKR)

> Adjust these to your own rates. Ranges reflect typical Sri Lanka project pricing for
> work of this scope; the final quote should be a single agreed figure.

| Package | Scope | Estimated cost (LKR) |
|---------|-------|----------------------|
| **Phase 1 — Core marketplace** | Everything in Phase 1 above, deployed & live | **250,000 – 450,000** |
| **Phase 2 — Premium + payments** | Paid listings, gateway, admin panel, messaging | **200,000 – 400,000** |
| Maintenance (optional) | Hosting management, updates, support | 15,000 – 30,000 / month |

**Payment terms (suggested):** 50% advance to begin, 50% on delivery. Hosting and
domain costs billed separately at actuals.

## 7. What's already done

A working Phase-1 foundation is built and running on the test server: database, models,
ad posting with image upload, cascading location dropdowns, category-specific fields,
search & filters, the listing/detail pages, and a category-section homepage — deployed
in Docker. This significantly de-risks the timeline above.

## 8. Next step

On your approval of scope and budget, I'll confirm the start date and share a short
project plan. Happy to walk through a live demo of the current build at any time.

---

*Thank you — looking forward to building this with you.*
**Vinod Lakmal · vinod@boh.city · merkeisolutions.com**
