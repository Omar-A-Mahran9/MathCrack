<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('front.page_title') }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
      --light-color: #f8f9fa;
      --dark-color: #2c3e50;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: #333;
      background-color: var(--light-color);
      padding-top: 80px;
      @if(app()->getLocale() == 'ar') text-align: right; @endif
    }

    .no-break { white-space: nowrap; display: inline-block; }

    .main-header {
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    .main-header.scrolled {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .main-header__inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      position: relative;
    }

    .logo img { height: 50px; width: auto; }

    .main-menu__list {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 30px;
      align-items: center;
      @if(app()->getLocale() == 'ar') direction: rtl; @endif
    }

    .main-menu__list a {
      text-decoration: none;
      color: var(--dark-color);
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
      white-space: nowrap;
    }

    .main-menu__list a:hover { color: var(--secondary-color); }

    .main-menu__list a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      @if(app()->getLocale() == 'ar') right: 0; @else left: 0; @endif
      width: 0;
      height: 2px;
      background: var(--secondary-color);
      transition: width 0.3s ease;
    }

    .main-menu__list a:hover::after { width: 100%; }

    .main-menu-wrapper {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }

    .header-right{
      display:flex;
      align-items:center;
      gap:12px;
    }

    .header-login-btn{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding:10px 18px;
      border-radius:999px;
      background:#fff;
      border:1px solid rgba(44,62,80,0.20);
      color:var(--primary-color);
      text-decoration:none;
      font-weight:600;
      transition:transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
      white-space:nowrap;
    }

    .header-login-btn:hover{
      background:rgba(52,152,219,0.08);
      border-color:rgba(52,152,219,0.35);
      box-shadow:0 10px 20px rgba(0,0,0,0.08);
      transform:translateY(-1px);
      color:var(--primary-color);
    }

    .header-join-btn {
      background: var(--secondary-color);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 999px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      white-space: nowrap;
      display: inline-flex;
      align-items:center;
      gap:10px;
    }

    .header-join-btn:hover {
      background: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      color: white;
    }

    .language-switch {
      @if(app()->getLocale() == 'ar') margin-right: 10px; @else margin-left: 10px; @endif
    }

    .language-switch .btn {
      background: rgba(0,0,0,0.04);
      border: 1px solid rgba(0,0,0,0.08);
      color: var(--dark-color);
      border-radius: 20px;
      padding: 5px 15px;
      font-size: 0.9rem;
      white-space: nowrap;
    }

    .language-switch .btn:hover { background: rgba(0,0,0,0.06); }

    .hero-section {
      background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.8)),
        url('https://images.unsplash.com/photo-1635070041078-e363dbe005cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
      padding: 50px 0 50px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before,
    .stats-section::before,
    .professor-header::before,
    .cta-section::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.3;
      pointer-events: none;
    }

    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      line-height: 1.2;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      position: relative;
      z-index: 1;
    }

    .hero-subtitle {
      font-size: 1.5rem;
      margin-bottom: 30px;
      opacity: 0.9;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      position: relative;
      z-index: 1;
    }

    .hero-highlight {
      color: #f1c40f;
      position: relative;
      display: inline-block;
      white-space: nowrap;
    }

    .hero-highlight::after {
      content: '';
      position: absolute;
      bottom: 5px;
      left: 0;
      width: 100%;
      height: 8px;
      background: rgba(241, 196, 15, 0.3);
      z-index: -1;
    }

    .btn-hero {
      background: #f1c40f;
      color: var(--primary-color);
      border: none;
      padding: 15px 40px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      cursor: pointer;
      position: relative;
      z-index: 1;
      white-space:nowrap;
    }

    .btn-hero:hover {
      background: #f39c12;
      color: white;
      transform: translateY(-5px);
      box-shadow: 0 15px 25px rgba(0,0,0,0.3);
    }

    .btn-hero i { transition: transform 0.3s ease; }

    .btn-hero:hover i {
      @if(app()->getLocale() == 'ar') transform: translateX(-5px); @else transform: translateX(5px); @endif
    }

    .hidden-section {
      display: none;
      opacity: 0;
      transform: translateY(50px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .hidden-section.show {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .btn-hero.active { background: var(--primary-color); color: white; }
    .btn-hero.active i { transform: rotate(180deg); }

    html { scroll-behavior: smooth; }

    .section-title {
      text-align: center;
      margin-bottom: 60px;
      position: relative;
      z-index: 1;
    }

    .section-title h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 15px;
      white-space: nowrap;
    }

    .section-title p {
      font-size: 1.1rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }

    .stats-section {
      padding: 40px 0;
      background: var(--primary-color);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .stat-item {
      text-align: center;
      padding: 40px 20px;
      border-radius: 15px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stat-item:hover {
      transform: translateY(-10px);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .stat-number {
      font-size: 3.5rem;
      font-weight: 700;
      color: #f1c40f;
      margin-bottom: 10px;
      line-height: 1;
    }

    .stat-label { font-size: 1.2rem; opacity: 0.9; font-weight: 500; white-space: nowrap; }

    .professor-name-hero {
      font-size: 3rem;
      font-weight: 700;
      color: #ffdd44;
      text-align: center;
      margin-top: 4px;
      margin-bottom: 1rem;
      letter-spacing: 1px;
    }

    .professor-name-italic { font-style: italic; }

    .professor-section { padding: 30px 0; background: white; position: relative; overflow: visible; }

    .professor-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      overflow: visible;
      margin-top: 0;
      position: relative;
      z-index: 2;
      border: 1px solid rgba(0,0,0,0.05);
    }

    .professor-header {
      background: linear-gradient(135deg, var(--primary-color), #1a2530);
      color: white;
      padding: 60px 40px;
      border-radius: 20px 20px 0 0;
      position: relative;
      overflow: hidden;
    }

    .professor-name {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 10px;
      color: white;
      line-height: 1.2;
      position: relative;
      z-index: 1;
    }

    .professor-title {
      font-size: 1.4rem;
      opacity: 0.9;
      font-weight: 300;
      color: #f1c40f;
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }

    .professor-brief { position: relative; z-index: 1; }

    .professor-brief p {
      font-size: 1.1rem;
      line-height: 1.6;
      color: rgba(255,255,255,0.9);
      margin: 0;
    }

    .professor-image-container { width: 170px; height: 200px; margin: 0 auto; position: relative; }
    .professor-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      border: 6px solid rgba(255,255,255,0.2);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      background: #f8f9fa;
      object-position: center 20%;
    }

    .experience-content { padding: 0px 10px; background: white; }

    .courses-section { padding: 40px 0; background: var(--light-color); position: relative; }

    .course-card {
      background: white;
      border-radius: 15px;
      padding: 40px 30px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      height: 100%;
      transition: all 0.3s ease;
      text-align: center;
      border: 1px solid rgba(0,0,0,0.05);
      position: relative;
      overflow: hidden;
    }

    .course-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 5px;
      background: var(--secondary-color);
    }

    .course-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .course-icon { font-size: 3rem; color: var(--secondary-color); margin-bottom: 25px; }
    .course-card h4 { font-size: 1.5rem; margin-bottom: 15px; color: var(--primary-color); white-space: nowrap; }
    .course-card p { color: #666; line-height: 1.6; }


    /* =========================
       Flip course cards
    ========================= */
    .flip-card {
      height: 340px;
      perspective: 1100px;
      cursor: pointer;
      outline: none;
    }

    .flip-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 0.7s ease;
      transform-style: preserve-3d;
    }

    .flip-card.is-flipped .flip-card-inner {
      transform: rotateY(180deg);
    }

    [dir="rtl"] .flip-card.is-flipped .flip-card-inner {
      transform: rotateY(-180deg);
    }

    .flip-card-front,
    .flip-card-back {
      position: absolute;
      inset: 0;
      backface-visibility: hidden;
      border-radius: 15px;
      background: #fff;
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      border: 1px solid rgba(0,0,0,0.05);
      overflow: hidden;
    }

    .flip-card-front::before,
    .flip-card-back::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: var(--secondary-color);
    }

    .flip-card-front {
      padding: 40px 30px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .flip-card-back {
      padding: 28px 26px;
      transform: rotateY(180deg);
      text-align: start;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    [dir="rtl"] .flip-card-back {
      transform: rotateY(-180deg);
    }

    .flip-card-back h4 {
      color: var(--primary-color);
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 16px;
      text-align: center;
      white-space: nowrap;
    }

    .flip-course-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 0;
    }

    .flip-course-link {
      min-height: 46px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 8px;
      border-radius: 12px;
      background: rgba(52, 152, 219, 0.08);
      border: 1px solid rgba(52, 152, 219, 0.18);
      color: var(--primary-color);
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 700;
      text-align: center;
      line-height: 1.25;
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .flip-course-link:hover {
      background: var(--secondary-color);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 8px 18px rgba(52, 152, 219, 0.22);
    }

    .flip-course-link i {
      font-size: 0.8rem;
      flex: 0 0 auto;
    }

    .flip-hint {
      margin-top: 16px;
      text-align: center;
      font-size: 0.9rem;
      color: #777;
    }

    @media (max-width: 768px) {
      .flip-card {
        height: 390px;
      }
    }

    .cta-section {
      background: linear-gradient(135deg, var(--secondary-color), #2980b9);
      color: white;
      padding: 40px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .cta-title {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
      white-space: nowrap;
    }

    .cta-section .lead {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 700px;
      margin: 0 auto 30px;
      position: relative;
      z-index: 1;
    }

    .cta-button {
      background: white;
      color: var(--secondary-color);
      border: none;
      padding: 18px 45px;
      font-size: 1.2rem;
      font-weight: 600;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      position: relative;
      z-index: 1;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      @if(app()->getLocale() == 'ar') flex-direction: row-reverse; @endif
      white-space:nowrap;
    }

    .cta-button:hover {
      background: var(--primary-color);
      color: white;
      transform: translateY(-5px);
      box-shadow: 0 15px 25px rgba(0,0,0,0.3);
    }

    .contact-main-section { padding: 30px 0; background: white; }
    .contact-main-section .section-title { margin-bottom: 25px; }

    .contact-main-card {
      background: linear-gradient(135deg, #2980b9, #2980b9);
      border-radius: 20px;
      padding:20px 10px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      margin-top: 20px;
    }

    .contact-main-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 10px;
      color: var(--primary-color);
      text-align: center;
      white-space: nowrap;
    }

    .contact-main-subtitle {
      font-size: 1.2rem;
      color: #666;
      text-align: center;
      margin-bottom: 10px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      opacity: 1;
    }

    .contact-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .contact-info-box {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      text-align: center;
      transition: all 0.3s ease;
      border: 1px solid rgba(0,0,0,0.05);
    }

    .contact-info-box:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }

    .contact-box-icon { font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 20px; }
    .contact-box-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 15px; color: var(--primary-color); }
    .contact-box-content { color: #555; line-height: 1.6; }

    .phone-numbers-text {
      font-family: monospace;
      font-weight: 600;
      color: var(--primary-color);
      text-align: center;
      padding: 10px 0;
    }

    .phone-numbers-text div { margin-bottom: 8px; }
    .phone-numbers-text div:last-child { margin-bottom: 0; }

    .main-footer { background: var(--primary-color); color: white; padding: 70px 0 30px; position: relative; }
    .footer-logo img { height: 60px; margin-bottom: 20px; }

    .footer-links h5 { margin-bottom: 20px; font-size: 1.2rem; color: white; white-space: nowrap; }

    .footer-links ul { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 12px; }

    .footer-links a {
      color: #ddd;
      text-decoration: none;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .footer-links a:hover { color: white; transform: translateX(5px); }

    .social-icons { display: flex; gap: 15px; margin-top: 20px; }

    .social-icons a {
      color: white;
      background: rgba(255,255,255,0.1);
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      border: 1px solid rgba(255,255,255,0.2);
    }

    .social-icons a:hover {
      background: var(--secondary-color);
      transform: translateY(-5px);
      border-color: var(--secondary-color);
    }

    .copyright {
      text-align: center;
      margin-top: 50px;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      color: #aaa;
      font-size: 0.9rem;
    }

    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: var(--secondary-color);
      color: white;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 1000;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      border: 2px solid white;
    }

    .back-to-top.show { opacity: 1; visibility: visible; }

    .back-to-top:hover {
      background: var(--primary-color);
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.3);
      color: white;
    }

    .fade-in { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .fade-in.visible { opacity: 1; transform: translateY(0); }

    @media (max-width: 768px) {
      .hero-title { font-size: 2.5rem; }
      .hero-subtitle { font-size: 1.2rem; }
      .professor-name { font-size: 2.2rem; }
      .main-menu__list { gap: 15px; }
      .experience-content { padding: 40px 20px; }
      .stat-number { font-size: 2.5rem; }
      .professor-header { padding: 50px 20px; }
      .cta-title { font-size: 2rem; }
      .contact-main-title { font-size: 2rem; }
      .contact-info-grid { grid-template-columns: 1fr; }
      .main-menu__list a { font-size: 0.9rem; }
      .main-menu-wrapper { position: static; transform: none; }
      .main-header__inner { flex-wrap: wrap; justify-content: center; text-align: center; }
      .logo { width:100%; margin-bottom: 15px; }
      .header-right { width:100%; justify-content:center; margin-bottom: 10px; }
    }

    @media (max-width: 576px) {
      .hero-title { font-size: 2rem; }
      .professor-name { font-size: 1.8rem; }
      .section-title h2 { font-size: 2rem; }
      .stat-number { font-size: 2rem; }
      .main-menu__list { flex-wrap: wrap; justify-content: center; gap: 10px 15px; }
      .header-right { flex-direction: column; gap: 10px; }
    }

    .hero-subtitle span { display: block; }

    [dir="rtl"] .dropdown-menu { text-align: right; }
    [dir="rtl"] .me-2 { margin-left: 0.5rem; margin-right: 0; }
    [dir="rtl"] .ms-2 { margin-right: 0.5rem; margin-left: 0; }
    [dir="rtl"] .text-end { text-align: left !important; }
    [dir="rtl"] .text-start { text-align: right !important; }

    .cv-block{ padding:50px 40px; background:#fff; }
    [dir="rtl"] .cv-block{ direction: rtl; }
    [dir="ltr"] .cv-block{ direction: ltr; }

    .cv-title{ color:var(--primary-color); font-size:1.8rem; font-weight:700; margin-bottom:18px; }
    .cv-section{ margin-bottom:10px; }

    .cv-subtitle{
      font-size:1.25rem;
      font-weight:700;
      color:var(--primary-color);
      margin-bottom:12px;
      padding-bottom:10px;
      border-bottom:2px solid rgba(0,0,0,0.06);
    }

    .cv-list{ list-style:none; margin:0; padding:0; }

    .cv-list > li{
      display:grid;
      gap:12px;
      align-items:center;
      padding:12px 0;
      border-bottom:1px solid rgba(0,0,0,0.06);
    }

    .cv-list > li:last-child{ border-bottom:0; }

    .cv-item{ min-width:0; }
    .cv-role{ font-weight:700; }
    .cv-meta{ color:#555; margin:0 6px; }
    .cv-place{ color:#111; font-weight:600; margin:0 6px; }

    .cv-date{
      display:inline-block;
      padding:2px 10px;
      border-radius:999px;
      background:rgba(52,152,219,0.10);
      border:1px solid rgba(52,152,219,0.20);
      color:#1a2b3a;
      font-weight:600;
      white-space:nowrap;
      direction:ltr;
      unicode-bidi:isolate;
    }

    [dir="ltr"] .cv-list{ counter-reset:cv; }

    [dir="ltr"] .cv-list > li{
      counter-increment:cv;
      grid-template-columns:34px 1fr auto;
      grid-template-areas:"no main date";
      align-items:start;
    }

    [dir="ltr"] .cv-list > li::before{
      content: counter(cv) ".";
      grid-area:no;
      font-weight:700;
      color:var(--primary-color);
      min-width:32px;
      line-height:1.4;
      text-align:right;
      direction:ltr;
      unicode-bidi:isolate;
    }

    [dir="ltr"] .cv-item{ grid-area:main; }
    [dir="ltr"] .cv-date{ grid-area:date; justify-self:end; }

    [dir="rtl"] .cv-list{ counter-reset:none; }

    [dir="rtl"] .cv-list > li{
      grid-template-columns:1fr auto;
      grid-template-areas:"main date";
      align-items:center;
    }

    [dir="rtl"] .cv-list > li::before{ content:none; display:none; }
    [dir="rtl"] .cv-item{ grid-area:main; text-align:right; }
    [dir="rtl"] .cv-date{ grid-area:date; justify-self:start; }

    @media (max-width:768px){
      .cv-block{ padding:10px 10px; }

      [dir="ltr"] .cv-list > li{
        grid-template-columns:34px 1fr;
        grid-template-areas:"no main" "date date";
        align-items:start;
      }

      [dir="ltr"] .cv-date{ justify-self:start; }

      [dir="rtl"] .cv-list > li{
        grid-template-columns:1fr;
        grid-template-areas:"main" "date";
      }

      [dir="rtl"] .cv-date{
        justify-self:start;
        margin-top:6px;
      }
    }

    /* ===== Home page final polish ===== */
    .main-header {
      border-bottom: 1px solid rgba(44, 62, 80, 0.08);
    }

    .main-menu__list a {
      font-size: 1.02rem;
      font-weight: 700;
      letter-spacing: .01em;
    }

    .header-login-btn {
      background: linear-gradient(135deg, #ffffff 0%, #eef7ff 100%);
      border: 1px solid rgba(52, 152, 219, 0.30);
      color: #1f3a56;
      padding: 11px 22px;
      font-size: 1rem;
      font-weight: 800;
      box-shadow: 0 8px 18px rgba(44, 62, 80, 0.07);
    }

    .header-login-btn i {
      font-size: 1.18rem;
      color: var(--secondary-color);
    }

    .header-login-btn:hover {
      background: linear-gradient(135deg, var(--secondary-color), #2279b9);
      color: #fff;
      border-color: transparent;
      box-shadow: 0 14px 28px rgba(52, 152, 219, 0.25);
    }

    .header-login-btn:hover i {
      color: #fff;
    }

    .header-join-btn {
      padding: 12px 28px;
      font-size: 1rem;
      font-weight: 800;
      box-shadow: 0 10px 22px rgba(52, 152, 219, 0.22);
    }

    #courses {
      background:
        radial-gradient(circle at top left, rgba(52, 152, 219, 0.10), transparent 32%),
        radial-gradient(circle at bottom right, rgba(241, 196, 15, 0.10), transparent 34%),
        #f7fbff;
    }

    #courses .section-title h2 {
      font-size: 2.75rem;
      font-weight: 900;
      letter-spacing: -.03em;
      color: #1f2d3d;
    }

    #courses .section-title p {
      font-size: 1.2rem;
      color: #4b5563;
      font-weight: 500;
    }

    #courses .flip-card {
      height: 370px;
    }

    #courses .flip-card-front,
    #courses .flip-card-back {
      border-radius: 22px;
      border: 1px solid rgba(52, 152, 219, 0.14);
      box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
      background:
        linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    #courses .flip-card-front::before,
    #courses .flip-card-back::before {
      height: 6px;
      background: linear-gradient(90deg, #2d9cdb, #4c43f5, #f1c40f);
    }

    #courses .flip-card-front::after {
      content: '';
      position: absolute;
      inset: 18px;
      border-radius: 18px;
      background:
        radial-gradient(circle at center, rgba(52, 152, 219, 0.12), transparent 42%);
      opacity: .9;
      pointer-events: none;
    }

    #courses .flip-card-front > * {
      position: relative;
      z-index: 2;
    }

    #courses .course-icon {
      width: 74px;
      height: 74px;
      border-radius: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 22px;
      background: linear-gradient(135deg, rgba(52,152,219,.13), rgba(76,67,245,.10));
      color: var(--secondary-color);
      font-size: 2.45rem;
      box-shadow: inset 0 0 0 1px rgba(52,152,219,.14);
    }

    #courses .flip-card-front h4 {
      font-size: 1.65rem;
      font-weight: 900;
      color: #1f2d3d;
      letter-spacing: -.02em;
      white-space: normal;
      line-height: 1.3;
      max-width: 320px;
    }

    #courses .flip-card-back {
      padding: 32px 28px;
    }

    #courses .flip-course-grid {
      gap: 13px;
    }

    #courses .flip-course-link {
      min-height: 58px;
      border-radius: 15px;
      background: #eef7ff;
      border: 1px solid rgba(52, 152, 219, 0.22);
      color: #1f2d3d;
      font-size: 1.03rem;
      font-weight: 900;
      letter-spacing: -.01em;
      box-shadow: 0 8px 16px rgba(15, 23, 42, 0.04);
    }

    #courses .flip-course-link i {
      font-size: 1rem;
      color: var(--secondary-color);
    }

    #courses .flip-course-link:hover {
      background: linear-gradient(135deg, var(--secondary-color), #2279b9);
      color: #fff;
      border-color: transparent;
      box-shadow: 0 12px 24px rgba(52, 152, 219, 0.25);
    }

    #courses .flip-course-link:hover i {
      color: #fff;
    }

    @media (max-width: 768px) {
      #courses .section-title h2 {
        font-size: 2.1rem;
        white-space: normal;
      }

      #courses .flip-card {
        height: 410px;
      }

      #courses .flip-course-link {
        font-size: .98rem;
      }
    }


    .coming-soon-link {
      background: linear-gradient(135deg, #f8fafc, #eef2f7) !important;
      border-color: rgba(44, 62, 80, 0.12) !important;
      color: #4b5563 !important;
      cursor: pointer;
    }

    .coming-soon-link i {
      color: #f39c12 !important;
    }

    .coming-soon-link:hover {
      background: linear-gradient(135deg, #fff7e6, #fff2cc) !important;
      color: #1f2d3d !important;
      box-shadow: 0 12px 24px rgba(243, 156, 18, 0.16) !important;
    }

    .coming-soon-link:hover i {
      color: #f39c12 !important;
    }

    .coming-soon-modal .modal-content {
      border: 0;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
    }

    .coming-soon-modal .modal-header {
      background: linear-gradient(135deg, var(--secondary-color), #2279b9);
      color: #fff;
      border-bottom: 0;
      padding: 22px 24px;
    }

    .coming-soon-modal .modal-title {
      font-weight: 900;
    }

    .coming-soon-modal .btn-close {
      filter: invert(1);
      opacity: .9;
    }

    .coming-soon-icon {
      width: 70px;
      height: 70px;
      border-radius: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #eef7ff;
      color: var(--secondary-color);
      font-size: 2rem;
      margin-bottom: 16px;
    }

    .coming-soon-course-name {
      font-weight: 900;
      color: var(--primary-color);
    }

  </style>
</head>

<body>
<header class="main-header" id="mainHeader">
  <div class="container">
    <div class="main-header__inner">
      <div class="logo">
        <a href="{{ route('login') }}">
          @if(isset($settings['logo']) && $settings['logo'])
            <img src="{{ asset($settings['logo']) }}" alt="{{ $settings['name'] ?? 'Math Expert' }}" height="50">
          @else
            <img src="https://via.placeholder.com/200x50/2c3e50/ffffff?text=MATH+EXPERT" alt="Math Expert">
          @endif
        </a>
      </div>

      <div class="main-menu-wrapper">
        <nav class="main-menu">
          <ul class="main-menu__list">
            <li><a href="#home">{{ __('front.nav_home') }}</a></li>
            <li><a href="#professor" class="professor-link">{{ __('front.nav_professor') }}</a></li>
            <li><a href="#stats">{{ __('front.nav_stats') }}</a></li>
            <li><a href="#courses">{{ __('front.nav_courses') }}</a></li>
            <li><a href="#contactMain">{{ __('front.nav_contact') }}</a></li>
          </ul>
        </nav>
      </div>

      <div class="header-right">
        <div class="language-switch">
          <div class="dropdown">
            <button class="btn btn-sm dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-globe me-1"></i>{{ strtoupper(app()->getLocale()) }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
              @foreach($headerLanguages ?? [] as $language)
                <li>
                  <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL($language->code, null, [], true) }}">
                    {{ $language->native }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>

        <a href="{{ route('login') }}" class="header-login-btn">
          <i class="fas fa-user-circle me-1"></i>
          Login
        </a>

        <a href="{{ route('register') }}?action=mock&redirect=mock-test" class="header-join-btn">
  <i class="fas fa-play me-1"></i>
  Start Free Mock
</a>
      </div>
    </div>
  </div>
</header>

<section class="hero-section" id="home">
  <div class="container">
    <div class="fade-in">
      <h1 class="hero-title">{{ __('front.hero_dsat_title') }}</h1>

      <p class="hero-subtitle">
        <span>{{ __('front.hero_dsat_line1') }}</span>
        <span>{{ __('front.hero_dsat_line2') }}</span>
      </p>

      <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('login') }}" class="btn-hero">
           Join Now
        </a>

        <a href="#courses" class="btn-hero active">
  {{ __('front.cta_courses') }}
</a>
      </div>

      <div class="mt-3" style="opacity:0.9; position:relative; z-index:1;">
        {{ __('front.hero_trust_line') }}
      </div>

      <div class="mt-4" style="position:relative; z-index:1;">
        <div class="professor-name-hero" style="font-size:2.1rem; margin-bottom:0.25rem;">
          <span class="hero-highlight no-break professor-name-italic">{{ __('front.prof_name') }}</span>
        </div>
        <div style="opacity:0.9;">
          {{ __('front.prof_short_cred') }}
        </div>
      </div>
    </div>
  </div>
</section>

<section class="professor-section hidden-section" id="professor">
  <div class="container">
    <div class="section-title fade-in" style="margin-bottom: 40px;">
      <h2 class="no-break" style="color: var(--primary-color); font-size: 2.5rem;">{{ __('front.meet_expert') }}</h2>
      <p style="max-width: 600px; margin: 0 auto;">{{ __('front.math_specialist') }}</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="professor-card">
          <div class="professor-header">
            <div class="row align-items-center">
              <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="professor-image-container">
                  <img
                    src="{{ asset('assets/themes/default/front/images/2.webp') }}"
                    alt="{{ __('front.prof_name') }}"
                    class="professor-image"
                    onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'"
                  >
                </div>
              </div>

              <div class="col-md-8">
                <h1 class="professor-name no-break">{{ __('front.prof_name') }}</h1>
                <p class="professor-title">{{ __('front.prof_title') }}</p>

                <div class="professor-brief mt-4">
                  <p>{{ __('front.hero_line1') }}</p>
                  <p>{{ __('front.hero_line2') }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="experience-content cv-block">
            <h3 class="mb-4 cv-title">{{ __('front.cv_section_title') }}</h3>

            <div class="cv-section">
              <h4 class="cv-subtitle">{{ __('front.cv_qualifications') }}</h4>

              <ol class="cv-list">
                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_phd') }}</span>
                    <span class="cv-meta">{{ __('front.cv_pure_math') }}</span>
                    <span class="cv-place">{{ __('front.cv_assiut_university') }}</span>
                  </div>
                  <span class="cv-date">01/01/1990</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_msc') }}</span>
                    <span class="cv-meta">{{ __('front.cv_pure_math') }}</span>
                    <span class="cv-place">{{ __('front.cv_assiut_university') }}</span>
                  </div>
                  <span class="cv-date">07/07/1986</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_bsc') }}</span>
                    <span class="cv-meta">{{ __('front.cv_math') }}</span>
                    <span class="cv-place">{{ __('front.cv_assiut_university') }}</span>
                  </div>
                  <span class="cv-date">05/1982</span>
                </li>
              </ol>
            </div>

            <div class="cv-section mt-4">
              <h4 class="cv-subtitle">{{ __('front.cv_practical_experiences') }}</h4>

              <ol class="cv-list">
                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_professor') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_topology_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">15/01/2015 - Now</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_head') }}</span>
                    <span class="cv-meta">{{ __('front.cv_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">01/10/2011 – 14/02/2015</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_professor') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_topology_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_taibah_madinah') }}</span>
                  </div>
                  <span class="cv-date">13/10/2008 – 02/07/2011</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_head') }}</span>
                    <span class="cv-meta">{{ __('front.cv_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">29/10/2002 – 12/10/2008</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_professor') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_topology_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">09/09/2002 – 28/10/2002</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_assistant_prof') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_math_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">05/11/1996 – 10/09/2002</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_lecturer') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_math_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_azhar_assiut') }}</span>
                  </div>
                  <span class="cv-date">08/10/1992 – 04/11/1996</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_assistant_lecturer') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_math_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_assiut_university') }}</span>
                  </div>
                  <span class="cv-date">28/08/1986 – 01/01/1990</span>
                </li>

                <li>
                  <div class="cv-item">
                    <span class="cv-role">{{ __('front.cv_demonstrator') }}</span>
                    <span class="cv-meta">{{ __('front.cv_dept_math_faculty_science') }}</span>
                    <span class="cv-place">{{ __('front.cv_assiut_university') }}</span>
                  </div>
                  <span class="cv-date">21/12/1982 – 27/08/1986</span>
                </li>
              </ol>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<section class="courses-section" id="dsat-system" style="background:#fff;">
  <div class="container">
    <div class="section-title fade-in">
      <h2 class="no-break">Our Learning System</h2>
<p>Lessons, materials, progress tracking, and mock tests in one place.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-3">
        <div class="course-card fade-in">
          <div class="course-icon"><i class="fas fa-clock"></i></div>
          <h4 class="no-break">{{ __('front.system_item1_title') }}</h4>
          <p>{{ __('front.system_item1_desc') }}</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="course-card fade-in">
          <div class="course-icon"><i class="fas fa-layer-group"></i></div>
          <h4 class="no-break">{{ __('front.system_item2_title') }}</h4>
          <p>{{ __('front.system_item2_desc') }}</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="course-card fade-in">
          <div class="course-icon"><i class="fas fa-chart-line"></i></div>
          <h4 class="no-break">{{ __('front.system_item3_title') }}</h4>
          <p>{{ __('front.system_item3_desc') }}</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="course-card fade-in">
          <div class="course-icon"><i class="fas fa-clipboard-check"></i></div>
          <h4 class="no-break">{{ __('front.system_item4_title') }}</h4>
          <p>{{ __('front.system_item4_desc') }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="courses-section" id="courses">
  <div class="container">
    <div class="section-title fade-in">
      <h2 class="no-break">{{ __('front.programs_title') }}</h2>
      <p>{{ __('front.programs_desc') }}</p>
    </div>@php
  $isArabic = app()->getLocale() == 'ar';
  $loginUrl = route('login');

  $programCards = [
    [
      'icon' => 'fas fa-graduation-cap',
      'title' => $isArabic ? 'كورسات الدبلومة الأمريكية' : 'American Diploma',
      'courses' => [
        ['label' => 'Digital SAT', 'slug' => 'digital-sat', 'available' => true],
        ['label' => 'EST I', 'slug' => 'est-i', 'available' => false],
        ['label' => 'EST II', 'slug' => 'est-ii', 'available' => false],
        ['label' => 'ACT I', 'slug' => 'act-i', 'available' => false],
        ['label' => 'ACT II', 'slug' => 'act-ii', 'available' => false],
        ['label' => 'AP Math', 'slug' => 'ap-math', 'available' => false],
      ],
    ],
    [
      'icon' => 'fas fa-university',
      'title' => $isArabic ? 'الرياضيات الجامعية' : 'University Mathematics',
      'courses' => [
        ['label' => 'Calculus', 'slug' => 'calculus', 'available' => false],
        ['label' => 'Linear Algebra', 'slug' => 'linear-algebra', 'available' => false],
        ['label' => 'Statistics', 'slug' => 'statistics', 'available' => false],
        ['label' => 'University Math Support', 'slug' => 'university-math-support', 'available' => false],
      ],
    ],
    [
      'icon' => 'fas fa-chalkboard-teacher',
      'title' => $isArabic ? 'التحضير للاختبارات' : 'Exam Preparation',
      'courses' => [
        ['label' => $isArabic ? 'اختبارات محاكاة كاملة' : 'Full Mock Tests', 'slug' => 'full-mock-tests', 'available' => false],
        ['label' => $isArabic ? 'تدريب بزمن محدد' : 'Timed Practice', 'slug' => 'timed-practice', 'available' => false],
        ['label' => $isArabic ? 'مراجعة نهائية' : 'Final Revision', 'slug' => 'final-revision', 'available' => false],
        ['label' => $isArabic ? 'تقارير مستوى الطالب' : 'Student Progress Reports', 'slug' => 'student-progress-reports', 'available' => false],
      ],
    ],
  ];
@endphp

    <div class="row g-4">
      @foreach($programCards as $program)
        <div class="col-md-4">
          <div class="flip-card fade-in" tabindex="0" role="button" aria-label="{{ $program['title'] }}">
            <div class="flip-card-inner">

              <div class="flip-card-front">
                <div class="course-icon">
                  <i class="{{ $program['icon'] }}"></i>
                </div>
                <h4 class="no-break">{{ $program['title'] }}</h4>
              
              </div>

              <div class="flip-card-back">
                 <div class="flip-course-grid">
                  @foreach($program['courses'] as $course)
                    @php
                      $isAvailable = $course['available'] ?? false;
                      $courseUrl = auth()->check()
                          ? route('dashboard.users.courses', ['track' => $course['slug']])
                          : $loginUrl . '?track=' . urlencode($course['slug']);
                    @endphp

                    <a
                      href="{{ $isAvailable ? $courseUrl : 'javascript:void(0)' }}"
                      class="flip-course-link {{ $isAvailable ? '' : 'coming-soon-link' }}"
                      data-course="{{ $course['label'] }}"
                      aria-disabled="{{ $isAvailable ? 'false' : 'true' }}"
                    >
                      <i class="{{ $isAvailable ? 'fas fa-book-open' : 'fas fa-clock' }}"></i>
                      <span>{{ $course['label'] }}</span>
                    </a>
                  @endforeach
                </div>

                
              </div>

            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="cta-section" id="join">
  <div class="container">
    <div class="fade-in">
      <h2 class="cta-title no-break">{{ __('front.journey_title') }}</h2>
      <p class="lead" style="margin-bottom: 5px;">{{ __('front.journey_desc') }}</p>
      <p class="instructor-name-cta" style="font-weight: bold; font-style: italic; color: #f1c40f; font-size: 1.7rem; margin-top: 0; margin-bottom: 1.5rem;">{{ __('front.journey_instructor') }}</p>

      <a href="{{ route('register') }}?action=mock&redirect=mock-test" class="cta-button">
  Start Free Mock
  @if(app()->getLocale() == 'ar')
    <i class="fas fa-arrow-left"></i>
  @else
    <i class="fas fa-arrow-right"></i>
  @endif
</a>
      <div class="mt-3" style="opacity:0.9; position:relative; z-index:1;">
        {{ __('front.hero_trust_line') }}
      </div>
    </div>
  </div>
</section>

<section class="stats-section" id="stats">
  <div class="container">
    <div class="section-title fade-in">
      <h2 class="no-break">{{ __('front.impact_title') }}</h2>
      <p>{{ __('front.impact_desc') }}</p>
      <h4 style="font-weight: bold; font-style: italic; color: #f1c40f; font-size: 1.7rem;">{{ __('front.instructor_name') }}</h4>
    </div>

    <div class="row g-4" id="statsContainer">
      <div class="col-md-3 col-sm-6">
        <div class="stat-item fade-in">
          <div class="stat-number" id="studentsStat" data-count="{{ (int)($stats['satisfied_students'] ?? 0) }}">0</div>
          <div class="stat-label">{{ __('front.satisfied_students') }}</div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="stat-item fade-in">
          <div class="stat-number" id="testsStat" data-count="{{ (int)($stats['Practice_Tests'] ?? 0) }}">0</div>
          <div class="stat-label">{{ __('front.practice_tests') }}</div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="stat-item fade-in">
          <div class="stat-number" id="coursesStat" data-count="{{ (int)($stats['total_courses'] ?? 0) }}">0</div>
          <div class="stat-label">{{ __('front.courses') }}</div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="stat-item fade-in">
          <div class="stat-number" id="instructorsStat" data-count="{{ (int)($stats['expert_instructors'] ?? 0) }}">0</div>
          <div class="stat-label">{{ __('front.expert_instructors') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="contact-main-section" id="contactMain">
  <div class="container">
    <div class="section-title fade-in">
      <h2 class="contact-main-title">{{ __('front.contact_title') }}</h2>
      <p class="contact-main-subtitle">{{ __('front.contact_subtitle') }}</p>
    </div>

    <div class="contact-main-card">
      <div class="contact-info-grid">
        <div class="contact-info-box fade-in">
          <div class="contact-box-icon"><i class="fas fa-map-marker-alt"></i></div>
          <h3 class="contact-box-title">{{ __('front.location_title') }}</h3>
          <div class="contact-box-content">
            <p><strong>{{ __('front.location_onsite') }}</strong></p>
            <p><strong>{{ __('front.location_online') }}</strong></p>
          </div>
        </div>

        <div class="contact-info-box fade-in">
          <div class="contact-box-icon"><i class="fas fa-phone"></i></div>
          <h3 class="contact-box-title">{{ __('front.phone_title') }}</h3>
          <div class="contact-box-content">
            <div class="phone-numbers-text">
              <div>+201060509026</div>
              <div>+201023560301</div>
            </div>
          </div>
        </div>

        <div class="contact-info-box fade-in">
          <div class="contact-box-icon"><i class="fas fa-clock"></i></div>
          <h3 class="contact-box-title">{{ __('front.availability_title') }}</h3>
          <div class="contact-box-content">
            <p><strong>{{ __('front.availability_desc') }}</strong></p>
            <p><strong>{{ __('front.availability_note') }}</strong></p>
          </div>
        </div>
      </div>

      <div class="text-center">
        <a href="{{ route('register') }}?action=mock&redirect=mock-test" class="btn btn-primary btn-lg">
  <i class="fas fa-user-graduate me-2"></i> Start Free Mock
</a>
      </div>
    </div>
  </div>
</section>

<footer class="main-footer dark-footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 mb-4">
        <div class="footer-logo">
          @if(isset($settings['logo']) && $settings['logo'])
            <img src="{{ asset($settings['logo']) }}" alt="{{ $settings['name'] ?? 'Math Expert' }}" height="60" style="filter: brightness(0) invert(1);">
          @else
            <img src="https://via.placeholder.com/200x50/ffffff/3498db?text=MATH+EXPERT" alt="Logo" style="filter: brightness(0) invert(1);">
          @endif
        </div>
        <p style="color: rgba(255, 255, 255, 0.9);">{{ __('front.footer_tagline') }}</p>
        <div class="social-icons">
          @if(isset($settings['facebook']) && $settings['facebook'])
            <a href="{{ $settings['facebook'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
          @endif
          @if(isset($settings['twitter']) && $settings['twitter'])
            <a href="{{ $settings['twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a>
          @endif
          @if(isset($settings['linkedin']) && $settings['linkedin'])
            <a href="{{ $settings['linkedin'] }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
          @endif
          @if(isset($settings['youtube']) && $settings['youtube'])
            <a href="{{ $settings['youtube'] }}" target="_blank"><i class="fab fa-youtube"></i></a>
          @endif
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="footer-links">
          <h5 class="no-break" style="color: white;">{{ __('front.quick_links') }}</h5>
          <ul>
            <li><a href="#home" style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-home"></i> {{ __('front.nav_home') }}</a></li>
            <li><a href="#professor" class="professor-link" style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-user-graduate"></i> {{ __('front.about_professor') }}</a></li>
            <li><a href="#stats" style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-chart-bar"></i> {{ __('front.nav_stats') }}</a></li>
            <li><a href="#courses" style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-book"></i> {{ __('front.nav_courses') }}</a></li>
            <li><a href="#contactMain" style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-address-book"></i> {{ __('front.nav_contact') }}</a></li>
            <li>
              <a href="{{ route('login') }}" style="color: #3498db; font-weight: 600;">
                <i class="fas fa-sign-in-alt"></i> Login
              </a>
            </li>
            <li>
 <a href="{{ route('register') }}?action=mock&redirect=mock-test"  style="color: rgba(255, 255, 255, 0.9);">
    <i class="fas fa-play"></i> Start Free Mock
  </a>
</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="footer-links">
          <h5 class="no-break" style="color: white;">{{ __('front.contact_info') }}</h5>
          <ul>
            <li style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-map-marker-alt"></i> Asyute, Egypt</li>
            <li style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-phone"></i> +201060509026</li>
            <li style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-phone"></i> +201023560301</li>
            <li style="color: rgba(255, 255, 255, 0.9);"><i class="fas fa-envelope"></i>
              @if(isset($settings['email1']) && $settings['email1'])
                {{ $settings['email1'] }}
              @else
                info@mathexpert.com
              @endif
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="copyright" style="border-top: 1px solid rgba(255, 255, 255, 0.2); color: rgba(255, 255, 255, 0.7);">
      <p>&copy; {{ date('Y') }} Math Expert. {{ __('front.copyright') }}</p>
      <p class="mt-2">
        <a href="{{ route('login') }}" style="color: #3498db; text-decoration: none;">
          <i class="fas fa-user me-1"></i> Login
        </a>
      </p>
    </div>
  </div>
</footer>


<div class="modal fade coming-soon-modal" id="comingSoonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-clock me-2"></i>
          Coming Soon
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="coming-soon-icon">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <h4 class="coming-soon-course-name" id="comingSoonCourseName">Course</h4>
        <p class="mb-0 text-muted">
          This course is not available yet. It will be added soon.
        </p>
      </div>
      <div class="modal-footer justify-content-center border-0 pb-4">
        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<a href="#" class="back-to-top" id="backToTop">
  <i class="fas fa-arrow-up"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('mainHeader');

    window.addEventListener('scroll', function() {
      if (window.scrollY > 100) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    });

    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', function() {
      if (window.scrollY > 300) backToTopBtn.classList.add('show');
      else backToTopBtn.classList.remove('show');
    });

    backToTopBtn.addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    const professorSection = document.getElementById('professor');
    const learnMoreBtn = document.getElementById('learnMoreBtn');
    const professorLinks = document.querySelectorAll('.professor-link');

    function showProfessorSection(e) {
      if (e) e.preventDefault();
      if (!professorSection) return;

      professorSection.classList.add('show');
      professorSection.classList.remove('hidden-section');
      if (learnMoreBtn) learnMoreBtn.classList.add('active');

      const headerHeight = document.querySelector('.main-header')?.offsetHeight || 80;
      const sectionTop = professorSection.offsetTop - headerHeight;

      setTimeout(() => {
        window.scrollTo({ top: Math.max(0, sectionTop), behavior: 'smooth' });
      }, 300);
    }

    if (learnMoreBtn) learnMoreBtn.addEventListener('click', showProfessorSection);
    professorLinks.forEach(link => link.addEventListener('click', showProfessorSection));



    document.querySelectorAll('.coming-soon-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const courseName = this.getAttribute('data-course') || 'This course';
        const nameEl = document.getElementById('comingSoonCourseName');
        if (nameEl) nameEl.textContent = courseName;

        const modalEl = document.getElementById('comingSoonModal');
        if (modalEl && window.bootstrap) {
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.show();
        } else {
          alert(courseName + ' is coming soon.');
        }
      });
    });

    document.querySelectorAll('.flip-card').forEach(card => {
      card.addEventListener('click', function(e) {
        if (e.target.closest('.flip-course-link')) return;
        this.classList.toggle('is-flipped');
      });

      card.addEventListener('keydown', function(e) {
        if (e.target.closest('.flip-course-link')) return;
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.classList.toggle('is-flipped');
        }
      });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        if (targetId === '#professor') {
          showProfessorSection(e);
          return;
        }

        const targetElement = document.querySelector(targetId);
        if (!targetElement) return;

        const headerHeight = document.querySelector('.main-header')?.offsetHeight || 80;
        const targetTop = targetElement.offsetTop - headerHeight;

        window.scrollTo({ top: Math.max(0, targetTop), behavior: 'smooth' });
      });
    });

    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
      });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

    function animateCounter(element) {
      const target = parseInt(element.getAttribute('data-count'));
      const duration = 2000;
      const step = target / (duration / 16);
      let current = 0;

      const timer = setInterval(() => {
        current += step;
        if (current >= target) {
          element.textContent = target + '';
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current);
        }
      }, 16);
    }

    const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const statNumbers = entry.target.querySelectorAll('.stat-number');
        statNumbers.forEach(statNumber => animateCounter(statNumber));
        statsObserver.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    const statsContainer = document.getElementById('statsContainer');
    if (statsContainer) statsObserver.observe(statsContainer);

    if (window.location.hash === '#professor') {
      setTimeout(() => { showProfessorSection(); }, 500);
    }

    function applyRTLFixes() {
      const isRTL = document.documentElement.dir === 'rtl';

      if (isRTL) {
        document.querySelectorAll('.btn-hero i, .cta-button i').forEach(icon => {
          if (icon.classList.contains('fa-arrow-right')) {
            icon.classList.remove('fa-arrow-right');
            icon.classList.add('fa-arrow-left');
          }
        });

        document.querySelectorAll('.dropdown-menu').forEach(menu => {
          menu.classList.add('dropdown-menu-end');
        });
      }
    }

    applyRTLFixes();
  });
</script>
</body>
</html>