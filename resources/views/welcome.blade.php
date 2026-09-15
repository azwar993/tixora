<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TIXORA | Temukan Event Menarik di Kotamu!</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
    <!-- 1.BARU DITAMBAHKAN: Swiper CSS for carousel -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@9/swiper-bundle.min.css" />
<style>
    /* =========================================================
   TIXORA EVENT TICKETING
   STYLE.CSS
   ========================================================= */


/* =========================================================
   1. RESET & GLOBAL
   ========================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: "Inter", sans-serif;
    color: #17171d;
    background: #f7f7fa;
    line-height: 1.6;
    overflow-x: hidden;
}

button,
input,
textarea,
select {
    font-family: inherit;
}

button {
    cursor: pointer;
}

a {
    text-decoration: none;
    color: inherit;
}

img {
    display: block;
    width: 100%;
}

.container {
    width: min(1180px, 92%);
    margin: 0 auto;
}

section {
    position: relative;
}


/* =========================================================
   2. GLOBAL TYPOGRAPHY
   ========================================================= */

.section-heading {
    margin-bottom: 38px;
}

.section-heading.center-heading {
    text-align: center;
}

.section-heading h2 {
    margin-top: 7px;
    font-size: 35px;
    line-height: 1.15;
    letter-spacing: -1.4px;
}

.section-heading h2 span {
    color: #7257ff;
}

.section-heading p {
    color: #8a8a93;
    margin-top: 9px;
    font-size: 13px;
}

.section-label {
    display: inline-block;
    color: #7257ff;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 2px;
}

.center-heading {
    margin-bottom: 42px;
}


/* =========================================================
   3. NAVBAR
   ========================================================= */

.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 76px;
    z-index: 1500;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(18px);
    border-bottom: 1px solid #ececf1;
}

.navbar-container {
    height: 100%;
    display: flex;
    align-items: center;
    gap: 25px;
}

.logo {
    font-size: 27px;
    font-weight: 900;
    letter-spacing: -1.7px;
    color: #15151a;
}

.logo span {
    color: #7257ff;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 27px;
    margin-left: 24px;
}

.nav-link {
    position: relative;
    color: #6d6d76;
    font-size: 13px;
    font-weight: 700;
    transition: 0.25s ease;
}

.nav-link:hover,
.nav-link.active {
    color: #7257ff;
}

.nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: 50%;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #7257ff;
    transform: translateX(-50%);
}

.nav-actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
}

.location-button,
.login-button,
.register-button {
    border: none;
    min-height: 40px;
    border-radius: 10px;
    padding: 0 13px;
    font-size: 11px;
    font-weight: 800;
    transition: 0.25s ease;
}

.location-button {
    display: flex;
    align-items: center;
    gap: 7px;
    background: #f1efff;
    color: #6850eb;
}

.location-button:hover {
    background: #e9e5ff;
}

.arrow-small {
    font-size: 9px;
}

.login-button {
    background: transparent;
    color: #25252a;
}

.login-button:hover {
    background: #f2f2f4;
}

.register-button {
    background: #18181d;
    color: white;
}

.register-button:hover {
    background: #7257ff;
    transform: translateY(-1px);
}

.mobile-menu-button {
    display: none;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 9px;
    background: #f1f1f4;
    color: #27272d;
}


/* =========================================================
   4. USER PROFILE NAVBAR
   ========================================================= */

.profile-wrapper {
    position: relative;
}

.profile-button {
    border: 1px solid #e7e7ec;
    background: white;
    min-height: 40px;
    padding: 0 10px 0 6px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 11px;
    font-weight: 800;
}

.profile-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    color: white;
    background: #7257ff;
    font-size: 11px;
}

.profile-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 10px);
    width: 235px;
    background: white;
    border: 1px solid #e7e7ec;
    border-radius: 14px;
    padding: 10px;
    box-shadow: 0 18px 50px rgba(28, 27, 40, 0.12);
    display: none;
}

.profile-dropdown.active {
    display: block;
}

.dropdown-user {
    display: flex;
    gap: 10px;
    align-items: center;
    padding: 9px;
}

.dropdown-avatar {
    width: 37px;
    height: 37px;
    border-radius: 50%;
    background: #f0edff;
    color: #7257ff;
    display: grid;
    place-items: center;
}

.dropdown-user strong,
.dropdown-user small {
    display: block;
}

.dropdown-user strong {
    font-size: 12px;
}

.dropdown-user small {
    color: #999;
    font-size: 9px;
    margin-top: 2px;
}

.dropdown-divider {
    height: 1px;
    background: #efeff2;
    margin: 7px 0;
}

.profile-dropdown button {
    width: 100%;
    border: none;
    background: transparent;
    display: flex;
    gap: 10px;
    align-items: center;
    text-align: left;
    padding: 11px 10px;
    border-radius: 8px;
    font-size: 11px;
    color: #4a4a51;
}

.profile-dropdown button:hover {
    background: #f6f4ff;
    color: #7257ff;
}

.profile-dropdown .logout-menu {
    color: #d24f60;
}

.profile-dropdown .logout-menu:hover {
    background: #fff0f2;
    color: #d24f60;
}


/* =========================================================
   5. MOBILE NAVIGATION
   ========================================================= */

.mobile-navigation {
    position: fixed;
    top: 76px;
    left: 0;
    right: 0;
    z-index: 1400;
    display: none;
    flex-direction: column;
    background: white;
    border-bottom: 1px solid #e6e6eb;
    padding: 10px 5%;
    box-shadow: 0 13px 30px rgba(0, 0, 0, 0.05);
}

.mobile-navigation.active {
    display: flex;
}

.mobile-navigation a {
    padding: 12px 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
}

.mobile-navigation a:hover {
    background: #f4f1ff;
    color: #7257ff;
}


/* =========================================================
   6. HERO
   ========================================================= */

.hero-section {
    min-height: 700px;
    margin-top: 76px;
    display: flex;
    align-items: center;
    background:
        url("https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=2000&q=90")
        center / cover no-repeat;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(
            90deg,
            rgba(10, 10, 18, 0.95) 0%,
            rgba(10, 10, 18, 0.84) 37%,
            rgba(10, 10, 18, 0.25) 100%
        );
}

.hero-container {
    position: relative;
    z-index: 2;
}

.hero-content {
    max-width: 690px;
}

.hero-location {
    display: inline-flex;
    gap: 7px;
    align-items: center;
    background: rgba(255, 255, 255, 0.11);
    border: 1px solid rgba(255, 255, 255, 0.13);
    color: rgba(255, 255, 255, 0.83);
    padding: 8px 13px;
    border-radius: 50px;
    font-size: 10px;
    backdrop-filter: blur(12px);
}

.hero-location i {
    color: #a895ff;
}

.hero-location strong {
    color: white;
}

.hero-content h1 {
    margin-top: 24px;
    color: white;
    font-size: clamp(48px, 6vw, 75px);
    line-height: 0.99;
    letter-spacing: -4px;
    max-width: 700px;
}

.hero-content h1 span {
    color: #a996ff;
}

.hero-description {
    max-width: 590px;
    color: rgba(255, 255, 255, 0.72);
    font-size: 15px;
    margin-top: 24px;
}

.hero-search {
    max-width: 675px;
    min-height: 62px;
    margin-top: 27px;
    background: white;
    border-radius: 13px;
    padding: 7px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.hero-search > i {
    color: #85858d;
    padding-left: 14px;
}

.hero-search input {
    flex: 1;
    border: none;
    outline: none;
    height: 47px;
    min-width: 0;
    font-size: 13px;
}

.hero-search button {
    min-height: 48px;
    border: none;
    border-radius: 9px;
    background: #7257ff;
    color: white;
    padding: 0 21px;
    font-size: 11px;
    font-weight: 800;
}

.hero-search button:hover {
    background: #5f46e9;
}

.popular-search {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
    margin-top: 15px;
    color: rgba(255, 255, 255, 0.56);
    font-size: 10px;
}

.popular-search button {
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.81);
    border-radius: 50px;
    padding: 6px 10px;
    font-size: 10px;
}

.popular-search button:hover {
    background: #7257ff;
    border-color: #7257ff;
    color: white;
}


/* =========================================================
   7. CATEGORIES
   ========================================================= */

.categories-section {
    padding: 82px 0;
    background: white;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
}

.category-card {
    border: 1px solid #e9e9ee;
    background: white;
    border-radius: 15px;
    padding: 23px 12px;
    text-align: center;
    transition: 0.25s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    border-color: #d7d0ff;
    box-shadow: 0 15px 35px rgba(74, 50, 180, 0.08);
}

.category-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    margin: 0 auto 12px;
    display: grid;
    place-items: center;
    font-size: 20px;
}

.music-icon {
    color: #7257ff;
    background: #f0edff;
}

.sports-icon {
    color: #2d9b4b;
    background: #eaf7ee;
}

.esports-icon {
    color: #328ad1;
    background: #eaf5fd;
}

.festival-icon {
    color: #e48f2b;
    background: #fff4e8;
}

.theater-icon {
    color: #cf557f;
    background: #ffeef4;
}

.all-icon {
    color: #67676e;
    background: #f0f0f3;
}

.category-card h3 {
    font-size: 13px;
}

.category-card p {
    margin-top: 4px;
    font-size: 10px;
    color: #92929a;
}


/* =========================================================
   8. EVENTS
   ========================================================= */

.events-section {
    background: #f7f7fa;
    padding: 85px 0 95px;
}

.event-heading {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 30px;
}

.view-all-button {
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #7257ff;
    font-size: 11px;
    font-weight: 800;
}

.view-all-button:hover {
    gap: 11px;
}

.event-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 19px;
}

.event-card {
    overflow: hidden;
    background: white;
    border-radius: 17px;
    border: 1px solid #e9e9ee;
    transition: 0.25s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 42px rgba(28, 26, 45, 0.08);
}

.event-card-image {
    position: relative;
    height: 215px;
    overflow: hidden;
}

.event-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.45s ease;
}

.event-card:hover .event-card-image img {
    transform: scale(1.055);
}

.event-status {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 6px 9px;
    border-radius: 7px;
    color: white;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: 0.8px;
}

.status-ongoing {
    background: #30a64c;
}

.status-coming {
    background: #7257ff;
}

.status-past {
    background: #62626b;
}

.favorite-button {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.92);
    color: #45454c;
    display: grid;
    place-items: center;
    transition: 0.25s ease;
}

.favorite-button:hover {
    color: #ef557b;
    transform: scale(1.04);
}

.favorite-button.active {
    color: #ef557b;
}

.event-card-content {
    padding: 18px;
}

.event-card-category {
    display: inline-block;
    color: #7257ff;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: 1.7px;
}

.event-card-title {
    margin: 7px 0 12px;
    font-size: 17px;
    line-height: 1.28;
    letter-spacing: -0.3px;
}

.event-card-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: #85858e;
    font-size: 10px;
}

.event-card-meta span {
    display: flex;
    align-items: center;
    gap: 7px;
}

.event-card-meta i {
    width: 12px;
    color: #97979f;
}

.event-card-bottom {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 10px;
    margin-top: 15px;
    padding-top: 14px;
    border-top: 1px solid #efeff3;
}

.price-label {
    display: block;
    color: #9999a0;
    font-size: 9px;
}

.event-card-price {
    display: block;
    margin-top: 2px;
    font-size: 15px;
}

.detail-event-button {
    border: none;
    background: #f0edff;
    color: #7257ff;
    border-radius: 9px;
    padding: 9px 12px;
    font-size: 10px;
    font-weight: 900;
}

.detail-event-button:hover {
    background: #7257ff;
    color: white;
}


/* =========================================================
   9. SUB EVENT SECTION
   ========================================================= */

.sub-event-heading {
    margin-top: 72px;
    margin-bottom: 22px;
}

.sub-event-heading h2 {
    margin-top: 5px;
    font-size: 28px;
    line-height: 1.1;
    letter-spacing: -1px;
}

.sub-event-heading h2 span {
    color: #7257ff;
}

.horizontal-event-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 19px;
}

.horizontal-event-grid .event-card-image {
    height: 185px;
}

.horizontal-event-grid .event-card-title {
    font-size: 16px;
}


/* =========================================================
   10. EMPTY STATE
   ========================================================= */

.empty-state {
    display: none;
    text-align: center;
    padding: 65px 15px;
}

.empty-state i {
    font-size: 42px;
    color: #aaa;
}

.empty-state h3 {
    margin-top: 12px;
    font-size: 18px;
}

.empty-state p {
    margin-top: 5px;
    color: #888;
    font-size: 11px;
}

.empty-state button {
    margin-top: 15px;
    border: none;
    background: #7257ff;
    color: white;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 10px;
    font-weight: 800;
}


/* =========================================================
   11. WHY TIXORA
   ========================================================= */

.why-section {
    padding: 88px 0;
    background: white;
}

.why-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.why-card {
    border: 1px solid #e9e9ee;
    border-radius: 16px;
    padding: 25px;
    transition: 0.25s ease;
}

.why-card:hover {
    box-shadow: 0 18px 35px rgba(34, 30, 54, 0.06);
    transform: translateY(-3px);
}

.why-icon {
    width: 50px;
    height: 50px;
    border-radius: 13px;
    background: #f0edff;
    color: #7257ff;
    display: grid;
    place-items: center;
    font-size: 19px;
}

.why-card h3 {
    margin-top: 17px;
    font-size: 16px;
}

.why-card p {
    margin-top: 7px;
    color: #85858d;
    font-size: 11px;
}


/* =========================================================
   12. ARTICLES
   ========================================================= */

.articles-section {
    padding: 86px 0;
    background: #f7f7fa;
}

.article-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 19px;
}

.article-card {
    background: white;
    overflow: hidden;
    border: 1px solid #e9e9ee;
    border-radius: 17px;
}

.article-card img {
    height: 215px;
    object-fit: cover;
}

.article-content {
    padding: 20px;
}

.article-category {
    color: #7257ff;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: 1.5px;
}

.article-content h3 {
    margin-top: 8px;
    font-size: 17px;
    line-height: 1.35;
}

.article-content p {
    margin-top: 7px;
    color: #86868f;
    font-size: 11px;
}

.article-content a {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    color: #7257ff;
    font-size: 10px;
    font-weight: 900;
}


/* =========================================================
   13. CTA
   ========================================================= */

.cta-section {
    padding: 72px 0;
    color: white;
    background:
        radial-gradient(
            circle at 85% 20%,
            rgba(114, 87, 255, 0.30),
            transparent 28%
        ),
        #16161c;
}

.cta-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 35px;
}

.cta-container h2 {
    max-width: 620px;
    margin-top: 7px;
    font-size: 43px;
    line-height: 1.1;
    letter-spacing: -1.7px;
}

.cta-container h2 span {
    color: #a895ff;
}

.cta-container p {
    color: rgba(255,255,255,0.62);
    margin-top: 9px;
    font-size: 12px;
}

.cta-button {
    border: none;
    display: flex;
    align-items: center;
    gap: 9px;
    background: #7257ff;
    color: white;
    border-radius: 9px;
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 900;
}

.cta-button:hover {
    background: #866fff;
}


/* =========================================================
   14. FOOTER
   ========================================================= */

.footer {
    background: #0e0e12;
    color: white;
    padding: 68px 0 20px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 50px;
}

.footer-brand > p {
    max-width: 340px;
    color: #87878f;
    margin-top: 14px;
    font-size: 11px;
}

.social-icons {
    display: flex;
    gap: 8px;
    margin-top: 18px;
}

.social-icons a {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    background: #1a1a20;
    color: #aaaab2;
}

.social-icons a:hover {
    color: white;
    background: #7257ff;
}

.footer-column {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.footer-column h4 {
    font-size: 12px;
    margin-bottom: 7px;
}

.footer-column a,
.footer-column p {
    color: #85858e;
    font-size: 10px;
}

.footer-column a:hover {
    color: white;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #202027;
    margin-top: 50px;
    padding-top: 18px;
    color: #68686f;
    font-size: 9px;
}


/* =========================================================
   15. MODAL SYSTEM
   ========================================================= */

.modal {
    position: fixed;
    inset: 0;
    z-index: 4000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
}

.modal.active {
    display: flex;
}

.modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(8, 8, 13, 0.68);
    backdrop-filter: blur(7px);
}

.modal-box {
    position: relative;
    z-index: 2;
    width: 100%;
    max-height: 94vh;
    overflow-y: auto;
    background: white;
    border-radius: 21px;
    box-shadow: 0 35px 100px rgba(0,0,0,0.23);
}

.close-modal {
    position: absolute;
    right: 16px;
    top: 16px;
    width: 35px;
    height: 35px;
    z-index: 5;
    border: none;
    border-radius: 50%;
    background: rgba(0,0,0,0.06);
    color: #5b5b63;
}

.close-modal:hover {
    background: #7257ff;
    color: white;
}

.auth-modal {
    max-width: 450px;
}

.small-modal {
    max-width: 440px;
}

.event-detail-modal {
    max-width: 730px;
}

.seat-modal {
    max-width: 620px;
}

.checkout-modal {
    max-width: 600px;
}

.payment-modal {
    max-width: 430px;
    text-align: center;
    padding: 35px 30px;
}

.success-modal {
    max-width: 430px;
    text-align: center;
    padding: 42px 30px 32px;
}

.digital-ticket-modal {
    max-width: 500px;
}

.profile-modal {
    max-width: 450px;
}

.orders-modal {
    max-width: 480px;
}


/* =========================================================
   16. AUTH MODAL
   ========================================================= */

.auth-header {
    text-align: center;
    padding: 35px 32px 8px;
}

.auth-logo {
    font-size: 24px;
    font-weight: 900;
}

.auth-logo span {
    color: #7257ff;
}

.auth-header h2 {
    margin-top: 9px;
    font-size: 25px;
}

.auth-header p {
    margin-top: 4px;
    color: #888;
    font-size: 11px;
}

.auth-form {
    padding: 11px 32px 0;
}

.auth-form > label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    margin: 13px 0 6px;
}

.input-box {
    height: 46px;
    padding: 0 12px;
    border: 1px solid #e5e5ea;
    border-radius: 9px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.input-box:focus-within {
    border-color: #7257ff;
}

.input-box i {
    color: #999;
    font-size: 12px;
}

.input-box input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 12px;
}

.auth-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 14px 0;
    font-size: 9px;
    color: #888;
}

.auth-options label {
    display: flex;
    align-items: center;
    gap: 5px;
}

.auth-options a {
    color: #7257ff;
    font-weight: 700;
}

.auth-submit {
    width: 100%;
    height: 47px;
    border: none;
    border-radius: 9px;
    background: #17171d;
    color: white;
    font-size: 11px;
    font-weight: 900;
}

.auth-submit:hover {
    background: #7257ff;
}

.auth-switch {
    text-align: center;
    color: #888;
    font-size: 10px;
    padding: 19px 32px 29px;
}

.auth-switch button {
    border: none;
    background: none;
    color: #7257ff;
    font-weight: 900;
}


/* =========================================================
   17. LOCATION MODAL
   ========================================================= */

.modal-title {
    padding: 31px 30px 12px;
}

.modal-title > i {
    color: #7257ff;
    font-size: 28px;
}

.modal-title h2 {
    margin-top: 7px;
    font-size: 25px;
}

.modal-title p {
    margin-top: 4px;
    color: #888;
    font-size: 11px;
}

.location-options {
    padding: 8px 25px 30px;
}

.location-options button {
    width: 100%;
    border: 1px solid #ebebef;
    background: white;
    padding: 13px;
    border-radius: 10px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #444;
}

.location-options button:hover {
    background: #f8f6ff;
    border-color: #d8d1ff;
    color: #7257ff;
}

.location-options button span {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 11px;
    font-weight: 800;
}

.location-options button > i {
    font-size: 9px;
    color: #aaa;
}


/* =========================================================
   18. EVENT DETAIL
   ========================================================= */

.detail-image-wrapper {
    height: 280px;
    overflow: hidden;
}

.detail-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-detail-content {
    padding: 25px;
}

.detail-category {
    color: #7257ff;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 1.6px;
}

.event-detail-content > h2 {
    margin-top: 6px;
    font-size: 29px;
    line-height: 1.2;
    letter-spacing: -0.8px;
}

.event-information {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 21px;
}

.event-information > div {
    display: flex;
    gap: 10px;
}

.event-information > div > i {
    color: #7257ff;
    font-size: 17px;
    margin-top: 2px;
}

.event-information small,
.event-information strong {
    display: block;
}

.event-information small {
    color: #a0a0a7;
    font-size: 9px;
}

.event-information strong {
    font-size: 11px;
}

.detail-description {
    margin-top: 25px;
}

.detail-description h3,
.lineup-section h3,
.price-section h3 {
    font-size: 15px;
}

.detail-description p {
    margin-top: 5px;
    color: #7f7f87;
    font-size: 11px;
}

.lineup-section {
    margin-top: 24px;
}

.lineup-list {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
    margin-top: 9px;
}

.lineup-tag {
    padding: 7px 10px;
    background: #f0edff;
    color: #7257ff;
    border-radius: 50px;
    font-size: 9px;
    font-weight: 800;
}

.price-section {
    margin-top: 28px;
}

.price-heading {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 20px;
}

.price-heading p,
.price-heading > span {
    color: #9b9ba2;
    font-size: 9px;
}

.ticket-list {
    display: grid;
    gap: 8px;
    margin-top: 10px;
}

.ticket-option {
    border: 1px solid #e8e8ed;
    background: white;
    border-radius: 10px;
    padding: 13px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: 0.2s ease;
}

.ticket-option:hover {
    border-color: #7257ff;
    background: #faf9ff;
}

.ticket-option-left {
    text-align: left;
}

.ticket-option-left strong,
.ticket-option-left small {
    display: block;
}

.ticket-option-left strong {
    font-size: 12px;
}

.ticket-option-left small {
    color: #999;
    margin-top: 2px;
    font-size: 9px;
}

.ticket-option-price {
    color: #7257ff;
    font-size: 12px;
    font-weight: 900;
}


/* =========================================================
   19. SEAT MODAL
   ========================================================= */

.stage {
    width: 58%;
    padding: 10px;
    margin: 11px auto 28px;
    border-radius: 8px;
    background: #17171d;
    color: white;
    text-align: center;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 1px;
}

.venue-map {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding: 0 26px;
}

.seat-row {
    display: flex;
    justify-content: center;
    gap: 7px;
}

.seat {
    width: 47px;
    height: 40px;
    border: none;
    border-radius: 7px;
    background: #eaf7ed;
    color: #27823e;
    font-size: 9px;
    font-weight: 900;
    transition: 0.18s ease;
}

.seat:hover {
    transform: scale(1.06);
}

.seat.selected {
    background: #7257ff;
    color: white;
}

.seat.sold {
    background: #e9e9ed;
    color: #a3a3a8;
    cursor: not-allowed;
}

.seat-legend {
    display: flex;
    justify-content: center;
    gap: 18px;
    margin-top: 23px;
    color: #777;
    font-size: 9px;
}

.seat-legend span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.legend-seat {
    width: 13px;
    height: 13px;
    display: inline-block;
    border-radius: 4px;
}

.legend-seat.available {
    background: #eaf7ed;
}

.legend-seat.selected {
    background: #7257ff;
}

.legend-seat.sold {
    background: #e9e9ed;
}

.selected-seat {
    margin: 17px 26px 15px;
    padding: 11px;
    text-align: center;
    background: #f6f6f8;
    border-radius: 9px;
    color: #808087;
    font-size: 10px;
}

.selected-seat strong {
    color: #24242a;
}

.continue-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: calc(100% - 52px);
    margin: 0 26px 27px;
    height: 46px;
    border: none;
    background: #7257ff;
    color: white;
    border-radius: 9px;
    font-size: 10px;
    font-weight: 900;
}

.continue-button:disabled {
    background: #dddde3;
    cursor: not-allowed;
}


/* =========================================================
   20. CHECKOUT
   ========================================================= */

.checkout-body {
    padding: 2px 30px 30px;
}

.order-summary {
    padding: 14px;
    background: #f5f3ff;
    border-radius: 12px;
}

.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 7px 0;
}

.summary-row span {
    color: #86868e;
    font-size: 10px;
}

.summary-row strong {
    text-align: right;
    font-size: 10px;
}

.summary-total {
    border-top: 1px dashed #ddd9f4;
    margin-top: 5px;
    padding-top: 11px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-total span {
    font-size: 11px;
    font-weight: 700;
}

.summary-total strong {
    font-size: 17px;
    color: #7257ff;
}

.payment-section {
    margin-top: 22px;
}

.payment-section h3 {
    font-size: 14px;
}

.payment-options {
    display: grid;
    gap: 8px;
    margin-top: 10px;
}

.payment-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #e7e7ec;
    border-radius: 10px;
    padding: 11px;
    transition: 0.2s ease;
}

.payment-option:hover {
    border-color: #cfc8ff;
}

.payment-option:has(input:checked) {
    border-color: #7257ff;
    background: #faf9ff;
}

.payment-option input {
    accent-color: #7257ff;
}

.payment-icon {
    width: 37px;
    height: 37px;
    border-radius: 9px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
}

.payment-icon.qris {
    background: #eeeaff;
    color: #7257ff;
}

.payment-icon.wallet {
    background: #eaf7ed;
    color: #2a9c4d;
}

.payment-icon.bank {
    background: #eaf5fd;
    color: #348dcc;
}

.payment-icon.card {
    background: #fff1e8;
    color: #e59436;
}

.payment-info strong,
.payment-info small {
    display: block;
}

.payment-info strong {
    font-size: 11px;
}

.payment-info small {
    font-size: 8px;
    color: #999;
    margin-top: 2px;
}

.payment-check {
    margin-left: auto;
    color: #7257ff;
    opacity: 0;
    font-size: 10px;
}

.payment-option:has(input:checked) .payment-check {
    opacity: 1;
}

.payment-detail {
    margin-top: 12px;
}

.payment-detail-box {
    background: #f8f8fa;
    border-radius: 9px;
    padding: 11px;
    color: #7c7c84;
    font-size: 9px;
}

.payment-detail-box strong {
    color: #25252b;
}

.pay-button {
    width: 100%;
    height: 47px;
    border: none;
    border-radius: 9px;
    background: #17171d;
    color: white;
    margin-top: 15px;
    font-size: 10px;
    font-weight: 900;
}

.pay-button:hover {
    background: #7257ff;
}


/* =========================================================
   21. QRIS PAYMENT
   ========================================================= */

.payment-success-icon {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: #f0edff;
    color: #7257ff;
    display: grid;
    place-items: center;
    margin: 0 auto 15px;
    font-size: 25px;
}

.payment-modal h2 {
    font-size: 22px;
}

.payment-modal > p {
    color: #888;
    font-size: 10px;
    margin-top: 5px;
}

.qris-code {
    width: 220px;
    height: 220px;
    padding: 9px;
    margin: 20px auto 14px;
    background: white;
    border: 1px solid #ddd;
    display: grid;
    grid-template-columns: repeat(21, 1fr);
}

.qris-code span {
    display: block;
}

.qris-amount {
    display: block;
    color: #7257ff;
    font-size: 19px;
}

.qris-expired {
    display: block;
    color: #aaa;
    font-size: 8px;
    margin-top: 4px;
}

.payment-confirm-button {
    width: 100%;
    height: 45px;
    margin-top: 20px;
    border: none;
    border-radius: 8px;
    background: #7257ff;
    color: white;
    font-size: 10px;
    font-weight: 900;
}

.payment-cancel-button {
    width: 100%;
    height: 42px;
    margin-top: 7px;
    border: none;
    background: transparent;
    color: #999;
    font-size: 10px;
}


/* =========================================================
   22. SUCCESS
   ========================================================= */

.success-icon {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    margin: 0 auto 17px;
    background: #e7f8eb;
    color: #2ca249;
    display: grid;
    place-items: center;
    font-size: 29px;
}

.success-modal h2 {
    font-size: 24px;
}

.success-modal > p {
    margin-top: 5px;
    color: #888;
    font-size: 11px;
}

.order-code {
    margin-top: 19px;
    padding: 11px;
    border-radius: 9px;
    background: #f6f6f8;
    color: #8b8b92;
    font-size: 10px;
}

.order-code strong {
    color: #7257ff;
}

.success-ticket-button {
    width: 100%;
    height: 46px;
    border: none;
    border-radius: 9px;
    background: #7257ff;
    color: white;
    margin-top: 14px;
    font-size: 10px;
    font-weight: 900;
}


/* =========================================================
   23. DIGITAL TICKET
   ========================================================= */

.digital-ticket {
    margin: 14px;
    overflow: hidden;
    border: 1px solid #dedee4;
    border-radius: 16px;
}

.ticket-top {
    background: #16161c;
    color: white;
    padding: 17px 19px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ticket-logo {
    font-size: 21px;
    font-weight: 900;
}

.ticket-logo span {
    color: #8d77ff;
}

.ticket-top > span {
    font-size: 8px;
    letter-spacing: 1.3px;
}

.ticket-main {
    padding: 21px;
}

.ticket-category {
    color: #7257ff;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: 1.5px;
}

.ticket-main h2 {
    margin-top: 6px;
    font-size: 23px;
    line-height: 1.2;
}

.ticket-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-top: 20px;
}

.ticket-info-grid small,
.ticket-info-grid strong {
    display: block;
}

.ticket-info-grid small {
    color: #999;
    font-size: 8px;
    letter-spacing: 1px;
}

.ticket-info-grid strong {
    font-size: 10px;
    margin-top: 2px;
}

.ticket-qr-wrapper {
    margin-top: 22px;
    padding: 17px;
    border-radius: 11px;
    background: #f7f7f9;
    text-align: center;
}

.ticket-qr {
    width: 185px;
    height: 185px;
    background: white;
    padding: 8px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(19, 1fr);
}

.ticket-qr span {
    display: block;
}

.ticket-qr-wrapper p {
    margin-top: 8px;
    color: #888;
    font-size: 8px;
}

.ticket-code {
    border-top: 1px dashed #d5d5d9;
    margin-top: 18px;
    padding-top: 14px;
    text-align: center;
    color: #999;
    font-size: 9px;
}

.ticket-code strong {
    color: #25252a;
}

.download-ticket-button {
    width: 100%;
    height: 44px;
    margin-top: 14px;
    border: none;
    border-radius: 8px;
    background: #17171d;
    color: white;
    font-size: 10px;
    font-weight: 900;
}

.download-ticket-button:hover {
    background: #7257ff;
}


/* =========================================================
   24. PROFILE
   ========================================================= */

.profile-header {
    text-align: center;
    padding: 35px 30px 20px;
}

.large-profile-avatar {
    width: 75px;
    height: 75px;
    margin: auto;
    border-radius: 50%;
    background: #7257ff;
    color: white;
    display: grid;
    place-items: center;
    font-size: 27px;
}

.profile-header h2 {
    font-size: 21px;
    margin-top: 11px;
}

.profile-header p {
    color: #999;
    font-size: 10px;
}

.profile-menu-list {
    padding: 5px 22px 28px;
}

.profile-menu-list button {
    width: 100%;
    border: none;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 11px;
    border-radius: 9px;
    color: #4b4b52;
    font-size: 10px;
}

.profile-menu-list button:hover {
    background: #f7f5ff;
    color: #7257ff;
}

.profile-menu-list button span {
    display: flex;
    gap: 9px;
    align-items: center;
}


/* =========================================================
   25. ORDERS / TICKETS HISTORY
   ========================================================= */

.orders-content,
.ticket-history {
    padding: 5px 25px 30px;
}

.empty-account {
    border: 1px dashed #dddde3;
    border-radius: 13px;
    text-align: center;
    padding: 45px 20px;
    color: #aaa;
}

.empty-account > i {
    font-size: 32px;
    color: #b1a9e9;
}

.empty-account h3 {
    color: #3b3b42;
    font-size: 15px;
    margin-top: 10px;
}

.empty-account p {
    font-size: 10px;
    margin-top: 4px;
}


/* =========================================================
   26. TOAST
   ========================================================= */

.toast {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 6000;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 215px;
    max-width: 340px;
    padding: 13px 15px;
    border-radius: 10px;
    background: #17171d;
    color: white;
    font-size: 10px;
    box-shadow: 0 15px 45px rgba(0,0,0,0.16);
    transform: translateY(100px);
    opacity: 0;
    transition: 0.3s ease;
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast i {
    color: #50c967;
}


/* =========================================================
   27. SCROLLBAR
   ========================================================= */

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f3;
}

::-webkit-scrollbar-thumb {
    background: #c9c5e0;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a99fec;
}


/* =========================================================
   28. RESPONSIVE - TABLET
   ========================================================= */

@media (max-width: 1050px) {

    .nav-menu {
        gap: 18px;
        margin-left: 8px;
    }

    .category-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .event-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .why-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .footer-grid {
        grid-template-columns: 1.7fr 1fr 1fr;
    }

    .footer-column:last-child {
        display: none;
    }

    .horizontal-event-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}


/* =========================================================
   29. RESPONSIVE - MOBILE NAV
   ========================================================= */

@media (max-width: 820px) {

    .nav-menu {
        display: none;
    }

    .mobile-menu-button {
        display: grid;
        place-items: center;
    }

    .location-button {
        display: none;
    }

    .hero-section {
        min-height: 640px;
    }

    .hero-content h1 {
        letter-spacing: -2.8px;
    }

    .article-grid {
        grid-template-columns: 1fr;
    }

    .cta-container {
        flex-direction: column;
        align-items: flex-start;
    }

}


/* =========================================================
   30. RESPONSIVE - MOBILE
   ========================================================= */

@media (max-width: 600px) {

    .container {
        width: min(92%, 100%);
    }

    .navbar {
        height: 70px;
    }

    .navbar-container {
        gap: 8px;
    }

    .logo {
        font-size: 24px;
    }

    .login-button {
        display: none;
    }

    .register-button {
        min-height: 37px;
        padding: 0 10px;
    }

    .mobile-menu-button {
        width: 37px;
        height: 37px;
    }

    .mobile-navigation {
        top: 70px;
    }

    .hero-section {
        min-height: 640px;
        margin-top: 70px;
    }

    .hero-content h1 {
        font-size: 45px;
        letter-spacing: -2.3px;
    }

    .hero-description {
        font-size: 13px;
    }

    .hero-search {
        min-height: auto;
        flex-wrap: wrap;
    }

    .hero-search input {
        height: 43px;
    }

    .hero-search button {
        width: 100%;
    }

    .category-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .event-grid {
        grid-template-columns: 1fr;
    }

    .horizontal-event-grid {
        grid-template-columns: 1fr;
    }

    .why-grid {
        grid-template-columns: 1fr;
    }

    .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .footer-column:last-child {
        display: flex;
    }

    .section-heading h2 {
        font-size: 29px;
    }

    .event-heading {
        align-items: flex-start;
    }

    .cta-container h2 {
        font-size: 33px;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 6px;
    }

    .event-information {
        grid-template-columns: 1fr;
    }

    .event-detail-content {
        padding: 20px;
    }

    .detail-image-wrapper {
        height: 220px;
    }

    .ticket-info-grid {
        grid-template-columns: 1fr 1fr;
    }

    .seat-row {
        gap: 4px;
    }

    .seat {
        width: 43px;
        height: 36px;
        font-size: 8px;
    }

    .venue-map {
        padding: 0 15px;
    }

    .selected-seat {
        margin-left: 15px;
        margin-right: 15px;
    }

    .continue-button {
        width: calc(100% - 30px);
        margin-left: 15px;
        margin-right: 15px;
    }

    .checkout-body {
        padding-left: 20px;
        padding-right: 20px;
    }

    .modal-title {
        padding-left: 22px;
        padding-right: 22px;
    }

    .auth-form {
        padding-left: 22px;
        padding-right: 22px;
    }

    .auth-switch {
        padding-left: 22px;
        padding-right: 22px;
    }

    .location-options {
        padding-left: 18px;
        padding-right: 18px;
    }

    .qris-code {
        width: 190px;
        height: 190px;
    }

    .ticket-qr {
        width: 165px;
        height: 165px;
    }

}


/* =========================================================
   31. VERY SMALL SCREEN
   ========================================================= */

@media (max-width: 400px) {

    .hero-content h1 {
        font-size: 39px;
    }

    .popular-search {
        gap: 5px;
    }

    .category-grid {
        gap: 9px;
    }

    .category-card {
        padding: 18px 8px;
    }

    .seat {
        width: 38px;
        height: 34px;
    }

    .ticket-info-grid {
        grid-template-columns: 1fr;
    }

}
</style>
</head>
<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar" id="navbar">

    <div class="container navbar-container">

        <!-- LOGO -->
        <a href="#home" class="logo">
            TIX<span>ORA</span>
        </a>

        <!-- NAVIGATION -->
        <nav class="nav-menu">

            <a href="#home" class="nav-link active">
                Home
            </a>

            <a href="#events" class="nav-link">
                Events
            </a>

            <a href="#categories" class="nav-link">
                Categories
            </a>

            <a href="#articles" class="nav-link">
                Articles
            </a>

        </nav>


        <!-- NAV ACTIONS -->
        <div class="nav-actions">

            <!-- LOCATION -->
            <button
                class="location-button"
                onclick="openLocationModal()"
            >
                <i class="fa-solid fa-location-dot"></i>
                <span id="selectedLocation">
                    JABODETABEK
                </span>
                <i class="fa-solid fa-chevron-down arrow-small"></i>
            </button>


            <!-- LOGIN -->
<a
    href="{{ route('login') }}"
    class="login-button"
    id="loginButton"
>
    Login
</a>

<!-- REGISTER -->
<a
    href="{{ route('register') }}"
    class="register-button"
    id="registerButton"
>
    Register
</a>

            <!-- <a href="admin.html" class="admin-login-link">
                Admin
            </a> -->


            <!-- USER PROFILE -->
            <div
                class="profile-wrapper"
                id="profileWrapper"
                style="display: none;"
            >

                <button
                    class="profile-button"
                    onclick="toggleProfileMenu()"
                >

                    <div class="profile-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <span id="profileName">
                        User
                    </span>

                    <i class="fa-solid fa-chevron-down arrow-small"></i>

                </button>


                <div
                    class="profile-dropdown"
                    id="profileDropdown"
                >

                    <div class="dropdown-user">

                        <div class="dropdown-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <strong id="dropdownName">
                                User
                            </strong>

                            <small id="dropdownEmail">
                                user@email.com
                            </small>
                        </div>

                    </div>


                    <div class="dropdown-divider"></div>


                    <button onclick="openProfileModal()">

                        <i class="fa-regular fa-user"></i>

                        My Profile

                    </button>


                    <button onclick="openOrdersModal()">

                        <i class="fa-solid fa-receipt"></i>

                        My Orders

                    </button>


                    <button onclick="openTicketsModal()">

                        <i class="fa-solid fa-ticket"></i>

                        My Tickets

                    </button>


                    <button onclick="showToast('Menu Favorites akan tersedia setelah database aktif.')">

                        <i class="fa-regular fa-heart"></i>

                        Favorites

                    </button>


                    <div class="dropdown-divider"></div>


                    <button
                        class="logout-menu"
                        onclick="logoutUser()"
                    >

                        <i class="fa-solid fa-right-from-bracket"></i>

                        Logout

                    </button>

                </div>

            </div>


            <!-- MOBILE MENU -->
            <button
                class="mobile-menu-button"
                onclick="toggleMobileMenu()"
            >
                <i class="fa-solid fa-bars"></i>
            </button>

        </div>

    </div>

</header>


<!-- MOBILE NAVIGATION -->

<div
    class="mobile-navigation"
    id="mobileNavigation"
>

    <a href="#home" onclick="closeMobileMenu()">
        Home
    </a>

    <a href="#events" onclick="closeMobileMenu()">
        Events
    </a>

    <a href="#categories" onclick="closeMobileMenu()">
        Categories
    </a>

    <a href="#articles" onclick="closeMobileMenu()">
        Articles
    </a>

</div>


<!-- =====================================================
     HERO
===================================================== -->

<main>

<section class="hero-section" id="home">

    <div class="hero-overlay"></div>


    <div class="container hero-container">

        <div class="hero-content">


            <!-- LOCATION BADGE -->

            <div class="hero-location">

                <i class="fa-solid fa-location-dot"></i>

                Event di

                <strong>
                    JABODETABEK
                </strong>

            </div>


            <!-- TITLE -->

            <h1>

                Temukan Event Menarik
                <br>

                <span>
                    di Kotamu!
                </span>

            </h1>


            <!-- DESCRIPTION -->

            <p class="hero-description">

                Temukan konser, pertandingan olahraga,
                festival, esports dan berbagai event
                menarik lainnya di sekitar kamu.

            </p>


            <!-- SEARCH -->

            <div class="hero-search">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari konser, artis, olahraga, festival..."
                >

                <button
                    onclick="searchEvents()"
                >
                    Search
                </button>

            </div>


            <!-- POPULAR SEARCH -->

            <div class="popular-search">

                <span>
                    Populer:
                </span>


                <button
                    onclick="searchKeyword('MPL')"
                >
                    MPL
                </button>


                <button
                    onclick="searchKeyword('Kahitna')"
                >
                    Kahitna
                </button>


                <button
                    onclick="searchKeyword('Nadin Amizah')"
                >
                    Nadin Amizah
                </button>


                <button
                    onclick="searchKeyword('Afgan')"
                >
                    Afgan
                </button>


                <button
                    onclick="searchKeyword('Music')"
                >
                    Music
                </button>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     QUICK CATEGORIES
===================================================== -->

<section
    class="categories-section"
    id="categories"
>

    <div class="container">


        <div class="section-heading">

            <div>

                <span class="section-label">
                    EXPLORE
                </span>

                <h2>

                    Cari Event Sesuai
                    <span>
                        Kegemaranmu
                    </span>

                </h2>

                <p>
                    Pilih kategori event yang ingin
                    kamu nikmati.
                </p>

            </div>

        </div>


        <div class="category-grid">


            <!-- MUSIC -->

            <button
                class="category-card"
                onclick="filterCategory('Music')"
            >

                <div class="category-icon music-icon">

                    <i class="fa-solid fa-music"></i>

                </div>

                <h3>
                    Music
                </h3>

                <p>
                    Konser & Live Music
                </p>

            </button>


            <!-- SPORTS -->

            <button
                class="category-card"
                onclick="filterCategory('Sports')"
            >

                <div class="category-icon sports-icon">

                    <i class="fa-solid fa-futbol"></i>

                </div>

                <h3>
                    Sports
                </h3>

                <p>
                    Football & Sports
                </p>

            </button>


            <!-- ESPORTS -->

            <button
                class="category-card"
                onclick="filterCategory('Esports')"
            >

                <div class="category-icon esports-icon">

                    <i class="fa-solid fa-gamepad"></i>

                </div>

                <h3>
                    Esports
                </h3>

                <p>
                    MPL & Gaming
                </p>

            </button>


            <!-- FESTIVAL -->

            <button
                class="category-card"
                onclick="filterCategory('Festival')"
            >

                <div class="category-icon festival-icon">

                    <i class="fa-solid fa-champagne-glasses"></i>

                </div>

                <h3>
                    Festival
                </h3>

                <p>
                    Festival & Entertainment
                </p>

            </button>


            <!-- THEATER -->

            <button
                class="category-card"
                onclick="filterCategory('Theater')"
            >

                <div class="category-icon theater-icon">

                    <i class="fa-solid fa-masks-theater"></i>

                </div>

                <h3>
                    Theater
                </h3>

                <p>
                    Show & Performance
                </p>

            </button>


            <!-- ALL -->

            <button
                class="category-card"
                onclick="showAllEvents()"
            >

                <div class="category-icon all-icon">

                    <i class="fa-solid fa-border-all"></i>

                </div>

                <h3>
                    All Events
                </h3>

                <p>
                    Lihat semua event
                </p>

            </button>

        </div>

    </div>

</section>


<!-- =====================================================
     EVENT SECTION
===================================================== -->

<section
    class="events-section"
    id="events"
>

    <div class="container">


        <!-- RECOMMENDED -->

        <div class="section-heading event-heading">

            <div>

                <span class="section-label">
                    RECOMMENDED
                </span>

                <h2>

                    Event Pilihan
                    <span>
                        Untukmu
                    </span>

                </h2>

            </div>


<!-- ++ -->
            <button
                class="view-all-button"
                onclick="showAllEvents()"
            >

                Lihat Semua

                <i class="fa-solid fa-arrow-right"></i>

            </button>

        </div>


        <!-- EVENTS -->

        <!-- BARU DITAMBAHKAN: carousel rekomendasi event -->
        <div class="event-carousel-wrapper">
            <div class="swiper event-carousel">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide px-2">
                        <article class="event-card event-card--carousel">
                            <div class="event-card-image">
                                <img src="{{ asset('images/event-1.jpg') }}" alt="Njonja Ati Soetji">
                            </div>
                            <div class="event-card-content">
                                <p class="event-card-category">Jakarta Selatan</p>
                                <h3 class="event-card-title">Njonja Ati Soetji</h3>
                                <p class="event-card-meta">Oleh Regina Art</p>
                                <div class="event-card-bottom">
                                    <div>
                                        <small class="price-label">Mulai dari</small>
                                        <span class="event-card-price">Rp180.000</span>
                                    </div>
                                    <button class="detail-event-button">Beli</button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide px-2">
                        <article class="event-card event-card--carousel">
                            <div class="event-card-image">
                                <img src="{{ asset('images/event-2.jpg') }}" alt="2026 HWANG IN YOUP FANMEETING">
                            </div>
                            <div class="event-card-content">
                                <p class="event-card-category">Jakarta Pusat</p>
                                <h3 class="event-card-title">2026 HWANG IN YOUP FANMEETING</h3>
                                <p class="event-card-meta">Oleh Three Mountains Ave</p>
                                <div class="event-card-bottom">
                                    <div>
                                        <small class="price-label">Mulai dari</small>
                                        <span class="event-card-price">Rp1.900.000</span>
                                    </div>
                                    <button class="detail-event-button">Beli</button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Tambah slide lain di sini / gunakan loop jika diperlukan -->
                </div>

                <!-- Navigation -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>

                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>


        <!-- EMPTY -->

        <div
            class="empty-state"
            id="emptyState"
        >

            <i class="fa-regular fa-face-frown"></i>

            <h3>
                Event tidak ditemukan
            </h3>

            <p>
                Coba gunakan kata pencarian lain.
            </p>

            <button
                onclick="showAllEvents()"
            >
                Lihat Semua Event
            </button>

        </div>


        <!-- ON GOING -->

        <div class="sub-event-heading">

            <div>

                <span class="section-label">
                    LIVE NOW
                </span>

                <h2>
                    Event <span>On Going</span>
                </h2>

            </div>

        </div>


        <div
            class="horizontal-event-grid"
            id="ongoingGrid"
        >
        </div>


        <!-- COMING SOON -->

        <div class="sub-event-heading">

            <div>

                <span class="section-label">
                    DON'T MISS IT
                </span>

                <h2>
                    Coming <span>Soon</span>
                </h2>

            </div>

        </div>


        <div
            class="horizontal-event-grid"
            id="comingGrid"
        >
        </div>


        <!-- PAST -->

        <div class="sub-event-heading">

            <div>

                <span class="section-label">
                    ARCHIVE
                </span>

                <h2>
                    Past <span>Event</span>
                </h2>

            </div>

        </div>


        <div
            class="horizontal-event-grid"
            id="pastGrid"
        >
        </div>

    </div>

</section>


<!-- =====================================================
     WHY TIXORA
===================================================== -->

<section class="why-section">

    <div class="container">


        <div class="section-heading center-heading">

            <span class="section-label">
                WHY TIXORA
            </span>

            <h2>
                Semua Event,
                <span>Satu Platform.</span>
            </h2>

            <p>
                Temukan pengalaman terbaik tanpa
                harus berpindah platform.
            </p>

        </div>


        <div class="why-grid">


            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <h3>
                    Mudah Mencari Event
                </h3>

                <p>
                    Cari event berdasarkan kategori,
                    lokasi, artis dan tanggal.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>

                <h3>
                    Digital Ticket
                </h3>

                <p>
                    Tiket tersimpan secara digital
                    dan dapat diakses kapan saja.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <h3>
                    QR Ticket
                </h3>

                <p>
                    Gunakan QR Code sebagai akses
                    masuk ke venue event.
                </p>

            </div>


            <div class="why-card">

                <div class="why-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <h3>
                    Pembayaran Digital
                </h3>

                <p>
                    Mendukung QRIS, e-wallet,
                    transfer bank dan kartu.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     ARTICLES
===================================================== -->

<section
    class="articles-section"
    id="articles"
>

    <div class="container">


        <div class="section-heading event-heading">

            <div>

                <span class="section-label">
                    TIXORA JOURNAL
                </span>

                <h2>
                    Artikel &
                    <span>Informasi Event</span>
                </h2>

            </div>

            <button class="view-all-button">

                Lihat Semua

                <i class="fa-solid fa-arrow-right"></i>

            </button>

        </div>


        <div class="article-grid">


            <article class="article-card">

                <img
                    src="https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=900&q=80"
                    alt="Tips Konser"
                >

                <div class="article-content">

                    <span class="article-category">
                        TIPS & TRICK
                    </span>

                    <h3>
                        Tips Membeli Tiket
                        Konser Online dengan Aman
                    </h3>

                    <p>
                        Panduan membeli tiket event
                        secara aman melalui platform digital.
                    </p>

                    <a href="#">
                        Baca Artikel
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>


            <article class="article-card">

                <img
                    src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&w=900&q=80"
                    alt="Sports Event"
                >

                <div class="article-content">

                    <span class="article-category">
                        GUIDE
                    </span>

                    <h3>
                        Mengenal Perbedaan
                        VIP, VVIP dan Regular
                    </h3>

                    <p>
                        Ketahui posisi dan fasilitas
                        dari berbagai jenis tiket event.
                    </p>

                    <a href="#">
                        Baca Artikel
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>


            <article class="article-card">

                <img
                    src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?auto=format&fit=crop&w=900&q=80"
                    alt="QR Ticket"
                >

                <div class="article-content">

                    <span class="article-category">
                        EVENT GUIDE
                    </span>

                    <h3>
                        Cara Masuk Venue
                        Menggunakan QR Ticket
                    </h3>

                    <p>
                        Panduan menggunakan tiket digital
                        saat check-in di venue.
                    </p>

                    <a href="#">
                        Baca Artikel
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </article>

        </div>

    </div>

</section>


<!-- =====================================================
     CTA
===================================================== -->

<section class="cta-section">

    <div class="container cta-container">

        <div>

            <span class="section-label">
                DISCOVER YOUR NEXT EVENT
            </span>

            <h2>
                Temukan Momen
                <span>Berikutnya.</span>
            </h2>

            <p>
                Jelajahi berbagai konser,
                olahraga dan event menarik.
            </p>

        </div>


        <button
            class="cta-button"
            onclick="showAllEvents()"
        >

            Explore Event

            <i class="fa-solid fa-arrow-right"></i>

        </button>

    </div>

</section>

</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="footer">

    <div class="container">


        <div class="footer-grid">


            <div class="footer-brand">

                <a href="#home" class="logo">
                    TIX<span>ORA</span>
                </a>

                <p>
                    Platform pemesanan tiket event
                    untuk menemukan pengalaman terbaik
                    di JABODETABEK dan sekitarnya.
                </p>


                <div class="social-icons">

                    <a href="#">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-facebook"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>

                </div>

            </div>


            <div class="footer-column">

                <h4>
                    Platform
                </h4>

                <a href="#events">
                    Events
                </a>

                <a href="#categories">
                    Categories
                </a>

                <a href="#articles">
                    Articles
                </a>

                <a href="#">
                    My Ticket
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Support
                </h4>

                <a href="#">
                    Help Center
                </a>

                <a href="#">
                    Contact Us
                </a>

                <a href="#">
                    Terms & Conditions
                </a>

                <a href="#">
                    Privacy Policy
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Location
                </h4>

                <p>
                    Jakarta
                </p>

                <p>
                    Bogor
                </p>

                <p>
                    Depok
                </p>

                <p>
                    Tangerang
                </p>

                <p>
                    Bekasi
                </p>

            </div>

        </div>


        <div class="footer-bottom">

            <span>
                © 2026 TIXORA. All Rights Reserved.
            </span>

            <span>
                Event Ticketing Platform
            </span>

        </div>

    </div>

</footer>


<!-- =====================================================
     LOGIN MODAL
===================================================== -->

<div
    class="modal"
    id="loginModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('loginModal')"
    ></div>


    <div class="modal-box auth-modal">

        <button
            class="close-modal"
            onclick="closeModal('loginModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="auth-header">

            <div class="auth-logo">
                TIX<span>ORA</span>
            </div>

            <h2>
                Welcome Back!
            </h2>

            <p>
                Login untuk melanjutkan ke TIXORA.
            </p>

        </div>


        <form
            class="auth-form"
            onsubmit="loginUser(event)"
        >

            <label>
                Email
            </label>

            <div class="input-box">

                <i class="fa-regular fa-envelope"></i>

                <input
                    type="email"
                    id="loginEmail"
                    placeholder="Masukkan email"
                    required
                >

            </div>


            <label>
                Password
            </label>

            <div class="input-box">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    id="loginPassword"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <div class="auth-options">

                <label>

                    <input type="checkbox">

                    Remember me

                </label>

                <a href="#">
                    Forgot Password?
                </a>

            </div>


            <button
                type="submit"
                class="auth-submit"
            >
                Login
            </button>

        </form>


        <div class="auth-switch">

            Belum punya akun?

            <button id="Register">
                Register
            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     REGISTER MODAL
===================================================== -->

<div
    class="modal"
    id="registerModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('registerModal')"
    ></div>


    <div class="modal-box auth-modal">

        <button
            class="close-modal"
            onclick="closeModal('registerModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="auth-header">

            <div class="auth-logo">
                TIX<span>ORA</span>
            </div>

            <h2>
                Create Account
            </h2>

            <p>
                Bergabung dan temukan event favoritmu.
            </p>

        </div>


        <form
            class="auth-form"
            onsubmit="registerUser(event)"
        >

            <label>
                Nama Lengkap
            </label>

            <div class="input-box">

                <i class="fa-regular fa-user"></i>

                <input
                    type="text"
                    id="registerName"
                    placeholder="Nama lengkap"
                    required
                >

            </div>


            <label>
                Email
            </label>

            <div class="input-box">

                <i class="fa-regular fa-envelope"></i>

                <input
                    type="email"
                    id="registerEmail"
                    placeholder="Email"
                    required
                >

            </div>


            <label>
                Password
            </label>

            <div class="input-box">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    id="registerPassword"
                    placeholder="Password"
                    required
                >

            </div>


            <label>
                Konfirmasi Password
            </label>

            <div class="input-box">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    id="registerConfirmPassword"
                    placeholder="Ulangi password"
                    required
                >

            </div>


            <button
                type="submit"
                class="auth-submit"
            >
                Create Account
            </button>

        </form>


        <div class="auth-switch">

            Sudah punya akun?

            <button onclick="switchToLogin()">
                Login
            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     LOCATION MODAL
===================================================== -->

<div
    class="modal"
    id="locationModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('locationModal')"
    ></div>


    <div class="modal-box small-modal">

        <button
            class="close-modal"
            onclick="closeModal('locationModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-title">

            <i class="fa-solid fa-location-dot"></i>

            <h2>
                Pilih Lokasi
            </h2>

            <p>
                Temukan event di kotamu.
            </p>

        </div>


        <div class="location-options">


            <button onclick="selectLocation('Jakarta')">

                <span>
                    <i class="fa-solid fa-city"></i>
                    Jakarta
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="selectLocation('Bogor')">

                <span>
                    <i class="fa-solid fa-city"></i>
                    Bogor
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="selectLocation('Depok')">

                <span>
                    <i class="fa-solid fa-city"></i>
                    Depok
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="selectLocation('Tangerang')">

                <span>
                    <i class="fa-solid fa-city"></i>
                    Tangerang
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="selectLocation('Bekasi')">

                <span>
                    <i class="fa-solid fa-city"></i>
                    Bekasi
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="selectLocation('JABODETABEK')">

                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    Semua JABODETABEK
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     EVENT DETAIL MODAL
===================================================== -->

<div
    class="modal"
    id="eventModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('eventModal')"
    ></div>


    <div class="modal-box event-detail-modal">

        <button
            class="close-modal"
            onclick="closeModal('eventModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="detail-image-wrapper">

            <img
                src=""
                alt="Event"
                id="detailEventImage"
            >

        </div>


        <div class="event-detail-content">


            <span
                class="detail-category"
                id="detailEventCategory"
            >
                MUSIC
            </span>


            <h2 id="detailEventTitle">
                Event
            </h2>


            <div class="event-information">


                <div>

                    <i class="fa-regular fa-calendar"></i>

                    <span>

                        <small>
                            Tanggal
                        </small>

                        <strong id="detailEventDate">
                            -
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-location-dot"></i>

                    <span>

                        <small>
                            Lokasi
                        </small>

                        <strong id="detailEventLocation">
                            -
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="fa-regular fa-clock"></i>

                    <span>

                        <small>
                            Waktu
                        </small>

                        <strong id="detailEventTime">
                            -
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-house"></i>

                    <span>

                        <small>
                            Venue
                        </small>

                        <strong id="detailEventVenue">
                            -
                        </strong>

                    </span>

                </div>

            </div>


            <div class="detail-description">

                <h3>
                    Tentang Event
                </h3>

                <p id="detailEventDescription">
                    -
                </p>

            </div>


            <div class="lineup-section">

                <h3>
                    Line Up / Performer
                </h3>

                <div
                    class="lineup-list"
                    id="lineupList"
                >
                </div>

            </div>


            <!-- PRICE LIST -->

            <div class="price-section">

                <div class="price-heading">

                    <div>

                        <h3>
                            Price List
                        </h3>

                        <p>
                            Pilih kategori tiket
                        </p>

                    </div>

                    <span>
                        Harga per tiket
                    </span>

                </div>


                <div
                    class="ticket-list"
                    id="ticketList"
                >
                </div>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     SEAT MODAL
===================================================== -->

<div
    class="modal"
    id="seatModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('seatModal')"
    ></div>


    <div class="modal-box seat-modal">

        <button
            class="close-modal"
            onclick="closeModal('seatModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-title">

            <span class="section-label">
                SELECT SEAT
            </span>

            <h2>
                Pilih Posisi / Seat
            </h2>

            <p id="selectedTicketText">
                -
            </p>

        </div>


        <div class="stage">
            STAGE
        </div>


        <div
            class="venue-map"
            id="venueMap"
        >
        </div>


        <div class="seat-legend">

            <span>
                <i class="legend-seat available"></i>
                Available
            </span>

            <span>
                <i class="legend-seat selected"></i>
                Selected
            </span>

            <span>
                <i class="legend-seat sold"></i>
                Sold
            </span>

        </div>


        <div class="selected-seat">

            Seat yang dipilih:

            <strong id="selectedSeatDisplay">
                -
            </strong>

        </div>


        <button
            class="continue-button"
            id="continueSeatButton"
            onclick="continueToCheckout()"
            disabled
        >

            Lanjut ke Checkout

            <i class="fa-solid fa-arrow-right"></i>

        </button>

    </div>

</div>


<!-- =====================================================
     CHECKOUT MODAL
===================================================== -->

<div
    class="modal"
    id="checkoutModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('checkoutModal')"
    ></div>


    <div class="modal-box checkout-modal">

        <button
            class="close-modal"
            onclick="closeModal('checkoutModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-title">

            <span class="section-label">
                CHECKOUT
            </span>

            <h2>
                Konfirmasi Pesanan
            </h2>

            <p>
                Periksa kembali detail tiket kamu.
            </p>

        </div>


        <div class="checkout-body">


            <div class="order-summary">

                <div class="summary-row">

                    <span>
                        Event
                    </span>

                    <strong id="checkoutEvent">
                        -
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Tiket
                    </span>

                    <strong id="checkoutTicket">
                        -
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Seat
                    </span>

                    <strong id="checkoutSeat">
                        -
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Harga
                    </span>

                    <strong id="checkoutPrice">
                        Rp0
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Admin Fee
                    </span>

                    <strong id="checkoutFee">
                        Rp0
                    </strong>

                </div>


                <div class="summary-total">

                    <span>
                        Total Pembayaran
                    </span>

                    <strong id="checkoutTotal">
                        Rp0
                    </strong>

                </div>

            </div>


            <!-- PAYMENT -->

            <div class="payment-section">

                <h3>
                    Pilih Metode Pembayaran
                </h3>


                <div class="payment-options">


                    <!-- QRIS -->

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="paymentMethod"
                            value="QRIS"
                            checked
                            onchange="selectPaymentMethod('QRIS')"
                        >

                        <div class="payment-icon qris">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>

                        <div class="payment-info">

                            <strong>
                                QRIS
                            </strong>

                            <small>
                                Scan QR Code
                            </small>

                        </div>

                        <i class="fa-solid fa-check payment-check"></i>

                    </label>


                    <!-- E WALLET -->

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="paymentMethod"
                            value="E-Wallet"
                            onchange="selectPaymentMethod('E-Wallet')"
                        >

                        <div class="payment-icon wallet">
                            <i class="fa-solid fa-wallet"></i>
                        </div>

                        <div class="payment-info">

                            <strong>
                                E-Wallet
                            </strong>

                            <small>
                                GoPay / DANA / OVO
                            </small>

                        </div>

                        <i class="fa-solid fa-check payment-check"></i>

                    </label>


                    <!-- BANK -->

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="paymentMethod"
                            value="Bank Transfer"
                            onchange="selectPaymentMethod('Bank Transfer')"
                        >

                        <div class="payment-icon bank">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>

                        <div class="payment-info">

                            <strong>
                                Bank Transfer
                            </strong>

                            <small>
                                Virtual Account
                            </small>

                        </div>

                        <i class="fa-solid fa-check payment-check"></i>

                    </label>


                    <!-- CARD -->

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="paymentMethod"
                            value="Credit Card"
                            onchange="selectPaymentMethod('Credit Card')"
                        >

                        <div class="payment-icon card">
                            <i class="fa-regular fa-credit-card"></i>
                        </div>

                        <div class="payment-info">

                            <strong>
                                Credit / Debit Card
                            </strong>

                            <small>
                                Visa / Mastercard
                            </small>

                        </div>

                        <i class="fa-solid fa-check payment-check"></i>

                    </label>

                </div>


                <!-- PAYMENT DETAIL -->

                <div
                    class="payment-detail"
                    id="paymentDetail"
                >
                </div>


                <button
                    class="pay-button"
                    onclick="processPayment()"
                >

                    Bayar Sekarang

                    <i class="fa-solid fa-lock"></i>

                </button>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     QRIS PAYMENT MODAL
===================================================== -->

<div
    class="modal"
    id="qrisModal"
>

    <div
        class="modal-overlay"
    ></div>


    <div class="modal-box payment-modal">

        <div class="payment-success-icon">
            <i class="fa-solid fa-qrcode"></i>
        </div>


        <h2>
            Pembayaran QRIS
        </h2>


        <p>
            Scan QR Code berikut menggunakan
            aplikasi pembayaran kamu.
        </p>


        <div
            class="qris-code"
            id="qrisCode"
        >
        </div>


        <strong
            class="qris-amount"
            id="qrisAmount"
        >
            Rp0
        </strong>


        <small class="qris-expired">
            Berlaku untuk simulasi prototype
        </small>


        <button
            class="payment-confirm-button"
            onclick="simulatePaymentSuccess()"
        >

            Saya Sudah Membayar

        </button>


        <button
            class="payment-cancel-button"
            onclick="closeModal('qrisModal')"
        >

            Batalkan

        </button>

    </div>

</div>


<!-- =====================================================
     PAYMENT SUCCESS MODAL
===================================================== -->

<div
    class="modal"
    id="successModal"
>

    <div
        class="modal-overlay"
    ></div>


    <div class="modal-box success-modal">

        <div class="success-icon">

            <i class="fa-solid fa-check"></i>

        </div>


        <h2>
            Pembayaran Berhasil!
        </h2>


        <p>
            Pesanan kamu berhasil diproses.
            Tiket digital telah dibuat.
        </p>


        <div class="order-code">

            Order ID:

            <strong id="successOrderId">
                -
            </strong>

        </div>


        <button
            class="success-ticket-button"
            onclick="openMyTicket()"
        >

            Lihat Tiket Saya

            <i class="fa-solid fa-ticket"></i>

        </button>

    </div>

</div>


<!-- =====================================================
     DIGITAL TICKET MODAL
===================================================== -->

<div
    class="modal"
    id="ticketModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('ticketModal')"
    ></div>


    <div class="modal-box digital-ticket-modal">

        <button
            class="close-modal"
            onclick="closeModal('ticketModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="digital-ticket">


            <div class="ticket-top">

                <div class="ticket-logo">
                    TIX<span>ORA</span>
                </div>

                <span>
                    DIGITAL TICKET
                </span>

            </div>


            <div class="ticket-main">

                <span class="ticket-category">
                    EVENT TICKET
                </span>


                <h2 id="ticketEventName">
                    -
                </h2>


                <div class="ticket-info-grid">


                    <div>

                        <small>
                            DATE
                        </small>

                        <strong id="ticketDate">
                            -
                        </strong>

                    </div>


                    <div>

                        <small>
                            VENUE
                        </small>

                        <strong id="ticketVenue">
                            -
                        </strong>

                    </div>


                    <div>

                        <small>
                            TICKET
                        </small>

                        <strong id="ticketType">
                            -
                        </strong>

                    </div>


                    <div>

                        <small>
                            SEAT
                        </small>

                        <strong id="ticketSeat">
                            -
                        </strong>

                    </div>

                </div>


                <div class="ticket-qr-wrapper">

                    <div
                        class="ticket-qr"
                        id="ticketQRCode"
                    >
                    </div>

                    <p>
                        Tunjukkan QR Code ini
                        saat check-in.
                    </p>

                </div>


                <div class="ticket-code">

                    Ticket ID:

                    <strong id="ticketCode">
                        -
                    </strong>

                </div>


                <button
                    class="download-ticket-button"
                    onclick="downloadTicket()"
                >

                    <i class="fa-solid fa-download"></i>

                    Download Ticket

                </button>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     PROFILE MODAL
===================================================== -->

<div
    class="modal"
    id="profileModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('profileModal')"
    ></div>


    <div class="modal-box profile-modal">

        <button
            class="close-modal"
            onclick="closeModal('profileModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="profile-header">

            <div class="large-profile-avatar">

                <i class="fa-solid fa-user"></i>

            </div>


            <h2 id="profileModalName">
                User
            </h2>


            <p id="profileModalEmail">
                user@email.com
            </p>

        </div>


        <div class="profile-menu-list">


            <button onclick="showToast('Fitur edit profile akan dihubungkan ke database.')">

                <span>
                    <i class="fa-regular fa-user"></i>
                    Data Profile
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="openOrdersModal()">

                <span>
                    <i class="fa-solid fa-receipt"></i>
                    Riwayat Pesanan
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="openTicketsModal()">

                <span>
                    <i class="fa-solid fa-ticket"></i>
                    Tiket Saya
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>


            <button onclick="showToast('Favorite akan tersimpan di database pada tahap backend.')">

                <span>
                    <i class="fa-regular fa-heart"></i>
                    Favorite Event
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     ORDERS MODAL
===================================================== -->
 
<div
    class="modal"
    id="ordersModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('ordersModal')"
    ></div>


    <div class="modal-box orders-modal">

        <button
            class="close-modal"
            onclick="closeModal('ordersModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-title">

            <span class="section-label">
                ACCOUNT
            </span>

            <h2>
                My Orders
            </h2>

            <p>
                Riwayat transaksi kamu.
            </p>

        </div>


        <div
            class="orders-content"
            id="ordersContent"
        >

            <div class="empty-account">

                <i class="fa-solid fa-receipt"></i>

                <h3>
                    Belum ada pesanan
                </h3>

                <p>
                    Pesanan kamu akan muncul
                    setelah melakukan pembelian.
                </p>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     TICKETS MODAL
===================================================== -->

<div
    class="modal"
    id="ticketsModal"
>

    <div
        class="modal-overlay"
        onclick="closeModal('ticketsModal')"
    ></div>


    <div class="modal-box orders-modal">

        <button
            class="close-modal"
            onclick="closeModal('ticketsModal')"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-title">

            <span class="section-label">
                ACCOUNT
            </span>

            <h2>
                My Tickets
            </h2>

            <p>
                Semua tiket digital kamu.
            </p>

        </div>


        <div
            class="ticket-history"
            id="ticketHistory"
        >

            <div class="empty-account">

                <i class="fa-solid fa-ticket"></i>

                <h3>
                    Belum ada tiket
                </h3>

                <p>
                    Tiket akan muncul setelah
                    pembayaran berhasil.
                </p>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     TOAST
===================================================== -->

<div
    class="toast"
    id="toast"
>

    <i class="fa-solid fa-circle-check"></i>

    <span id="toastMessage">
        Berhasil
    </span>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->
<!-- 2. BARU DITAMBAHKAN:lokal script proyek -->
<script src="script.js"></script>

<script>
// inline handlers (awalnya di dalam tag src) — baru ditambahkan
document.getElementById("loginButton").addEventListener("click", () => {
    window.location.href = "dashboard.html";
});

document.getElementById("registerButton").addEventListener("click", () => {
    window.location.href = "profile.html";
});
</script>

<!-- baru ditambahkan: Swiper JS (CDN) + inisialisasi carousel rekomendasi -->
<script src="https://unpkg.com/swiper@9/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Swiper untuk .event-carousel
    new Swiper('.event-carousel', {
        slidesPerView: 1.05,
        spaceBetween: 12,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: { slidesPerView: 2.05, spaceBetween: 12 },
            1024: { slidesPerView: 3.05, spaceBetween: 16 },
            1280: { slidesPerView: 4.05, spaceBetween: 18 }
        }
    });
});
</script>
<!-- SAMPAI SINI -->
</body>
</html>
