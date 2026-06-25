<?php
$chatbotUser = isset($_SESSION['user']) && $_SESSION['user'] !== '' ? $_SESSION['user'] : 'guest';
?>
<style>
/* Chatbot Design Tokens */
:root {
    --chat-primary: #e69500;
    --chat-primary-glow: rgba(230, 149, 0, 0.45);
    --chat-gradient-header: linear-gradient(135deg, #111a2e 0%, #1c325c 100%);
    --chat-body-bg: rgba(255, 255, 255, 0.88);
    --chat-card-bg: rgba(255, 255, 255, 0.95);
    --chat-text-main: #1f2937;
    --chat-text-light: #f9fafb;
    --chat-text-muted: #6b7280;
    --chat-border: rgba(226, 232, 240, 0.8);
    --chat-shadow-panel: 0 20px 50px rgba(17, 24, 39, 0.25), 0 4px 12px rgba(17, 24, 39, 0.08);
    --chat-shadow-bubble: 0 4px 12px rgba(0, 0, 0, 0.05);
    --chat-radius: 18px;
    --chat-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Floating Launcher */
.rm-chatbot-root {
    position: fixed;
    right: 104px;
    bottom: 24px;
    z-index: 1100;
    font-family: "Nunito", "Heebo", sans-serif;
    user-select: none;
}

.rm-chatbot-toggle {
    width: 62px;
    height: 62px;
    border: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffa800, #e69500);
    color: #fff;
    font-size: 1.5rem;
    box-shadow: 0 8px 24px var(--chat-primary-glow);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--chat-transition);
    position: relative;
}

.rm-chatbot-toggle:hover {
    transform: scale(1.08) rotate(5deg);
    box-shadow: 0 12px 28px var(--chat-primary-glow);
}

.rm-chatbot-toggle-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 3px 6px;
    border-radius: 999px;
    border: 2px solid #fff;
    animation: rm-chatbot-bounce 2s infinite;
}

.rm-chatbot-toggle-hint {
    position: absolute;
    right: 76px;
    bottom: 12px;
    background: #111a2e;
    color: #fff;
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateX(10px);
    pointer-events: none;
    transition: var(--chat-transition);
}

.rm-chatbot-toggle:hover .rm-chatbot-toggle-hint {
    opacity: 1;
    transform: translateX(0);
}

/* Chat Panel Container */
.rm-chatbot-panel {
    position: absolute;
    right: 0;
    bottom: 76px;
    width: min(390px, calc(100vw - 32px));
    height: min(640px, calc(100vh - 120px));
    background: var(--chat-body-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: var(--chat-radius);
    border: 1px solid var(--chat-border);
    box-shadow: var(--chat-shadow-panel);
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.9) translateY(20px);
    transform-origin: bottom right;
    opacity: 0;
    transition: var(--chat-transition);
}

.rm-chatbot-panel.is-open {
    display: flex;
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Chat Header */
.rm-chatbot-head {
    background: var(--chat-gradient-header);
    color: var(--chat-text-light);
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.rm-chatbot-profile-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.rm-chatbot-avatar-container {
    position: relative;
}

.rm-chatbot-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ffa800, #ffc83b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #111a2e;
}

.rm-chatbot-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background: #22c55e;
    border: 2px solid #111a2e;
    border-radius: 50%;
    animation: rm-chatbot-pulse 1.8s infinite;
}

.rm-chatbot-header-text {
    display: flex;
    flex-direction: column;
}

.rm-chatbot-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.rm-chatbot-sub {
    margin: 2px 0 0;
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.75);
    display: flex;
    align-items: center;
    gap: 4px;
}

.rm-chatbot-head-actions {
    display: flex;
    gap: 6px;
}

.rm-chatbot-head-btn {
    border: 0;
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border-radius: 8px;
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.8rem;
    transition: var(--chat-transition);
}

.rm-chatbot-head-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

.rm-chatbot-head-btn.active-control {
    background: var(--chat-primary);
    box-shadow: 0 0 8px var(--chat-primary-glow);
}

/* Chat Body */
.rm-chatbot-body {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: radial-gradient(circle at top, rgba(248, 250, 252, 0.85) 0%, rgba(241, 245, 249, 0.9) 100%);
    scroll-behavior: smooth;
}

.rm-chatbot-body::-webkit-scrollbar {
    width: 5px;
}
.rm-chatbot-body::-webkit-scrollbar-track {
    background: transparent;
}
.rm-chatbot-body::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.12);
    border-radius: 10px;
}

/* Message Bubbles */
.rm-chatbot-msg-row {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    align-items: flex-end;
}

.rm-chatbot-msg-row.user-row {
    flex-direction: row-reverse;
}

.rm-chatbot-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid var(--chat-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: var(--chat-primary);
    box-shadow: var(--chat-shadow-bubble);
    flex-shrink: 0;
}

.rm-chatbot-msg-row.user-row .rm-chatbot-msg-avatar {
    background: var(--chat-gradient-header);
    color: #fff;
}

.rm-chatbot-msg {
    max-width: 78%;
    padding: 12px 14px;
    border-radius: 16px;
    line-height: 1.5;
    font-size: 0.88rem;
    word-wrap: break-word;
    box-shadow: var(--chat-shadow-bubble);
    position: relative;
    animation: rm-chatbot-fade-in 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.rm-chatbot-msg-row.bot-row .rm-chatbot-msg {
    background: var(--chat-card-bg);
    color: var(--chat-text-main);
    border: 1px solid var(--chat-border);
    border-bottom-left-radius: 4px;
}

.rm-chatbot-msg-row.user-row .rm-chatbot-msg {
    background: linear-gradient(135deg, #1c325c, #111a2e);
    color: #fff;
    border-bottom-right-radius: 4px;
}

.rm-chatbot-msg-time {
    display: block;
    font-size: 0.65rem;
    margin-top: 4px;
    opacity: 0.6;
    text-align: right;
}

/* Rich Card Elements */
.rm-chatbot-rich-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid var(--chat-border);
    margin-top: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}

.rm-chatbot-rich-header {
    background: #f8fafc;
    padding: 8px 12px;
    font-weight: 800;
    font-size: 0.82rem;
    color: #1e325c;
    border-bottom: 1px solid var(--chat-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.rm-chatbot-rich-body {
    padding: 12px;
}

/* Horizontal Scroll for Categories */
.rm-chatbot-scroll-wrapper {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 6px 0;
    scroll-snap-type: x mandatory;
}
.rm-chatbot-scroll-wrapper::-webkit-scrollbar {
    height: 4px;
}
.rm-chatbot-scroll-wrapper::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.rm-chatbot-category-card {
    flex: 0 0 100px;
    background: #fff;
    border: 1px solid var(--chat-border);
    border-radius: 10px;
    padding: 8px;
    text-align: center;
    cursor: pointer;
    transition: var(--chat-transition);
}

.rm-chatbot-category-card:hover {
    border-color: var(--chat-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.rm-chatbot-category-card img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 4px;
}

.rm-chatbot-category-card span {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--chat-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Food List Cards */
.rm-chatbot-food-item {
    display: flex;
    gap: 10px;
    border-bottom: 1px solid #f1f5f9;
    padding: 8px 0;
    align-items: center;
}

.rm-chatbot-food-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.rm-chatbot-food-img {
    width: 46px;
    height: 46px;
    object-fit: cover;
    border-radius: 8px;
    background: #f1f5f9;
}

.rm-chatbot-food-info {
    flex: 1;
    min-width: 0;
}

.rm-chatbot-food-title {
    font-weight: 700;
    font-size: 0.8rem;
    color: var(--chat-text-main);
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.rm-chatbot-food-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 2px;
}

.rm-chatbot-food-price {
    font-weight: 800;
    font-size: 0.78rem;
    color: #e69500;
}

.rm-chatbot-food-stock {
    font-size: 0.65rem;
    font-weight: 600;
}

.rm-chatbot-food-stock.in-stock { color: #22c55e; }
.rm-chatbot-food-stock.low-stock { color: #f59e0b; }
.rm-chatbot-food-stock.out-stock { color: #ef4444; }

.rm-chatbot-add-cart-btn {
    border: 0;
    background: #1e325c;
    color: #fff;
    border-radius: 6px;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 4px 8px;
    cursor: pointer;
    transition: var(--chat-transition);
}

.rm-chatbot-add-cart-btn:hover {
    background: var(--chat-primary);
    transform: scale(1.05);
}

/* Coupon Copy Cards */
.rm-chatbot-coupon-card {
    background: #fff9eb;
    border: 1px dashed #ffa800;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.rm-chatbot-coupon-card:last-child {
    margin-bottom: 0;
}

.rm-chatbot-coupon-details {
    flex: 1;
}

.rm-chatbot-coupon-code {
    font-family: monospace;
    font-weight: 800;
    font-size: 0.95rem;
    color: #1e325c;
}

.rm-chatbot-coupon-name {
    font-size: 0.7rem;
    color: var(--chat-text-muted);
    margin-top: 1px;
}

.rm-chatbot-coupon-disc {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ef4444;
}

.rm-chatbot-coupon-copy {
    border: 1px solid #ffa800;
    background: #fff;
    color: #e69500;
    border-radius: 6px;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 4px 8px;
    cursor: pointer;
    transition: var(--chat-transition);
}

.rm-chatbot-coupon-copy:hover {
    background: #ffa800;
    color: #fff;
}

/* Order Tracker Stepper */
.rm-chatbot-tracker {
    margin-top: 6px;
}

.rm-chatbot-tracker-step {
    display: flex;
    gap: 12px;
    position: relative;
    padding-bottom: 12px;
}

.rm-chatbot-tracker-step:last-child {
    padding-bottom: 0;
}

.rm-chatbot-tracker-step::after {
    content: '';
    position: absolute;
    left: 8px;
    top: 18px;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
    z-index: 1;
}

.rm-chatbot-tracker-step:last-child::after {
    display: none;
}

.rm-chatbot-tracker-step.completed::after {
    background: #22c55e;
}

.rm-chatbot-tracker-node {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #cbd5e1;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.5rem;
    color: #fff;
    flex-shrink: 0;
    transition: var(--chat-transition);
}

.rm-chatbot-tracker-step.completed .rm-chatbot-tracker-node {
    background: #22c55e;
    border-color: #22c55e;
}

.rm-chatbot-tracker-step.active .rm-chatbot-tracker-node {
    background: var(--chat-primary);
    border-color: var(--chat-primary);
    box-shadow: 0 0 8px var(--chat-primary-glow);
    animation: rm-chatbot-ping 1.5s infinite;
}

.rm-chatbot-tracker-info {
    flex: 1;
    min-width: 0;
}

.rm-chatbot-tracker-title {
    font-weight: 700;
    font-size: 0.78rem;
    color: var(--chat-text-main);
    margin: 0;
}

.rm-chatbot-tracker-desc {
    font-size: 0.65rem;
    color: var(--chat-text-muted);
}

/* Typing Bubble Indicator */
.rm-chatbot-typing {
    display: inline-flex;
    gap: 4px;
    align-items: center;
    padding: 2px 4px;
}

.rm-chatbot-typing span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #94a3b8;
    animation: rm-chatbot-dot 1.2s infinite;
}

.rm-chatbot-typing span:nth-child(2) { animation-delay: 0.15s; }
.rm-chatbot-typing span:nth-child(3) { animation-delay: 0.3s; }

/* Quick Action Chips */
.rm-chatbot-quick {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 10px 16px 4px;
    background: linear-gradient(180deg, transparent 0%, rgba(241, 245, 249, 0.5) 100%);
}

.rm-chatbot-chip {
    border: 1px solid var(--chat-border);
    background: var(--chat-card-bg);
    color: #1e325c;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
    padding: 6px 12px;
    cursor: pointer;
    transition: var(--chat-transition);
    box-shadow: var(--chat-shadow-bubble);
}

.rm-chatbot-chip:hover {
    border-color: var(--chat-primary);
    background: var(--chat-primary);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px var(--chat-primary-glow);
}

/* Foot Controls & Input */
.rm-chatbot-foot {
    border-top: 1px solid var(--chat-border);
    background: #fff;
    padding: 12px;
    position: relative;
}

.rm-chatbot-input-row {
    display: flex;
    gap: 8px;
    align-items: center;
}

.rm-chatbot-input-container {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.rm-chatbot-input {
    width: 100%;
    border: 1px solid #d8e1f0;
    border-radius: 12px;
    padding: 10px 42px 10px 12px;
    font-size: 0.88rem;
    outline: none;
    transition: var(--chat-transition);
    color: var(--chat-text-main);
    font-family: inherit;
}

.rm-chatbot-input:focus {
    border-color: #a4bde5;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.rm-chatbot-emoji-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: 0;
    color: var(--chat-text-muted);
    cursor: pointer;
    font-size: 1.05rem;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--chat-transition);
}

.rm-chatbot-emoji-btn:hover {
    color: var(--chat-primary);
    transform: scale(1.1);
}

.rm-chatbot-send, .rm-chatbot-voice {
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 12px;
    color: #fff;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--chat-transition);
    flex-shrink: 0;
}

.rm-chatbot-voice {
    background: #f1f5f9;
    color: var(--chat-text-muted);
    border: 1px solid var(--chat-border);
}

.rm-chatbot-voice:hover {
    background: #e2e8f0;
    color: var(--chat-text-main);
}

.rm-chatbot-voice.listening {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
    animation: rm-chatbot-pulse-red 1.2s infinite;
}

.rm-chatbot-send {
    background: linear-gradient(135deg, #ffa800, #e69500);
    box-shadow: 0 4px 12px var(--chat-primary-glow);
}

.rm-chatbot-send:hover {
    box-shadow: 0 6px 16px var(--chat-primary-glow);
    transform: translateY(-1px);
}

.rm-chatbot-help {
    margin-top: 6px;
    color: var(--chat-text-muted);
    font-size: 0.7rem;
    text-align: center;
}

/* Emoji Drawer */
.rm-chatbot-emoji-drawer {
    position: absolute;
    bottom: 64px;
    right: 12px;
    width: 240px;
    background: #fff;
    border: 1px solid var(--chat-border);
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    padding: 10px;
    display: none;
    z-index: 10;
}

.rm-chatbot-emoji-drawer.is-open {
    display: block;
    animation: rm-chatbot-fade-in 0.2s ease;
}

.rm-chatbot-emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
}

.rm-chatbot-emoji-item {
    font-size: 1.2rem;
    text-align: center;
    padding: 4px;
    cursor: pointer;
    border-radius: 6px;
    transition: var(--chat-transition);
}

.rm-chatbot-emoji-item:hover {
    background: #f1f5f9;
    transform: scale(1.15);
}

/* Speak bubble voice active icon */
.rm-chatbot-read-speak-btn {
    border: 0;
    background: none;
    padding: 2px;
    cursor: pointer;
    font-size: 0.72rem;
    margin-left: auto;
    color: var(--chat-text-muted);
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.rm-chatbot-read-speak-btn:hover {
    color: var(--chat-primary);
}

/* Animations */
@keyframes rm-chatbot-bounce {
    0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
    40%, 43% { transform: translateY(-8px); }
    70% { transform: translateY(-4px); }
}

@keyframes rm-chatbot-pulse {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

@keyframes rm-chatbot-ping {
    0% { box-shadow: 0 0 0 0 rgba(230, 149, 0, 0.6); }
    70% { box-shadow: 0 0 0 8px rgba(230, 149, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(230, 149, 0, 0); }
}

@keyframes rm-chatbot-pulse-red {
    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

@keyframes rm-chatbot-dot {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1.1) translateY(-3px); opacity: 1; }
}

@keyframes rm-chatbot-fade-in {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Back to Top Restyle alignment override */
.back-to-top {
    width: 62px !important;
    height: 62px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    right: 24px !important;
    bottom: 24px !important;
    background: #e69500;
    border: 0 !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
    transition: var(--chat-transition) !important;
}

.back-to-top i {
    font-size: 1.2rem !important;
    line-height: 1 !important;
    margin: 0 !important;
}

/* Mobile Responsiveness */
@media (max-width: 576px) {
    .rm-chatbot-root {
        right: 86px;
        bottom: 12px;
    }
    .back-to-top {
        right: 12px !important;
        bottom: 12px !important;
    }
    .rm-chatbot-panel {
        position: fixed;
        left: 10px;
        right: 10px;
        width: auto;
        max-width: none;
        bottom: 84px;
        height: calc(100vh - 104px);
        max-height: none;
        border-radius: 16px;
    }
}
</style>

<div id="rmChatbotRoot" class="rm-chatbot-root">
    <!-- Floating Launcher -->
    <button id="rmChatToggle" class="rm-chatbot-toggle" type="button" aria-label="Open support chat">
        <i class="fas fa-robot"></i>
        <span class="rm-chatbot-toggle-badge">AI</span>
        <span class="rm-chatbot-toggle-hint">👋 Chat with us!</span>
    </button>

    <!-- Chat Panel -->
    <div id="rmChatPanel" class="rm-chatbot-panel" role="dialog" aria-modal="false" aria-label="Support chatbot">
        <!-- Header -->
        <div class="rm-chatbot-head">
            <div class="rm-chatbot-profile-info">
                <div class="rm-chatbot-avatar-container">
                    <div class="rm-chatbot-avatar">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <span class="rm-chatbot-status-dot"></span>
                </div>
                <div class="rm-chatbot-header-text">
                    <p class="rm-chatbot-title">Pasar Kita SmartBot</p>
                    <span class="rm-chatbot-sub"><i class="fas fa-circle" style="font-size: 5px; color:#22c55e;"></i> Online Assistant</span>
                </div>
            </div>
            <div class="rm-chatbot-head-actions">
                <button id="rmChatSound" class="rm-chatbot-head-btn active-control" type="button" title="Mute sounds">
                    <i class="fas fa-bell"></i>
                </button>
                <button id="rmChatVoice" class="rm-chatbot-head-btn" type="button" title="Read responses aloud">
                    <i class="fas fa-volume-mute"></i>
                </button>
                <button id="rmChatClear" class="rm-chatbot-head-btn" type="button" title="Clear chat history">
                    <i class="fas fa-redo-alt"></i>
                </button>
                <button id="rmChatClose" class="rm-chatbot-head-btn" type="button" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Scrollable Chat Body -->
        <div id="rmChatBody" class="rm-chatbot-body"></div>

        <!-- Quick Reply Chips -->
        <div id="rmChatQuick" class="rm-chatbot-quick"></div>

        <!-- Footer Control Area -->
        <div class="rm-chatbot-foot">
            <!-- Emoji Drawer -->
            <div id="rmEmojiDrawer" class="rm-chatbot-emoji-drawer">
                <div class="rm-chatbot-emoji-grid">
                    <span class="rm-chatbot-emoji-item">🍔</span>
                    <span class="rm-chatbot-emoji-item">🍕</span>
                    <span class="rm-chatbot-emoji-item">🍟</span>
                    <span class="rm-chatbot-emoji-item">🥤</span>
                    <span class="rm-chatbot-emoji-item">🥗</span>
                    <span class="rm-chatbot-emoji-item">📦</span>
                    <span class="rm-chatbot-emoji-item">👍</span>
                    <span class="rm-chatbot-emoji-item">🎉</span>
                    <span class="rm-chatbot-emoji-item">❤️</span>
                    <span class="rm-chatbot-emoji-item">👋</span>
                    <span class="rm-chatbot-emoji-item">😃</span>
                    <span class="rm-chatbot-emoji-item">😂</span>
                </div>
            </div>

            <!-- Input Row -->
            <div class="rm-chatbot-input-row">
                <button id="rmMicBtn" class="rm-chatbot-voice" type="button" aria-label="Speech Input">
                    <i class="fas fa-microphone"></i>
                </button>
                
                <div class="rm-chatbot-input-container">
                    <input id="rmChatInput" class="rm-chatbot-input" type="text" placeholder="Ask about menu, offers or enter Order ID..." maxlength="240">
                    <button id="rmEmojiToggle" class="rm-chatbot-emoji-btn" type="button" aria-label="Insert Emoji">
                        <i class="far fa-smile"></i>
                    </button>
                </div>

                <button id="rmChatSend" class="rm-chatbot-send" type="button" aria-label="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="rm-chatbot-help">Type "help" for a list of voice and text options.</div>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__rmChatbotBooted) return;
    window.__rmChatbotBooted = true;

    // Session & Config Local Keys
    const userKey = <?php echo json_encode($chatbotUser, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const siteUrl = <?php echo json_encode(SITEURL, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    const storageKey = `rmChatbotHistory_${userKey}`;
    const soundConfigKey = `rmChatbotSoundEnabled`;
    const voiceConfigKey = `rmChatbotVoiceEnabled`;

    // DOM Elements
    const panel = document.getElementById("rmChatPanel");
    const body = document.getElementById("rmChatBody");
    const toggle = document.getElementById("rmChatToggle");
    const closeBtn = document.getElementById("rmChatClose");
    const clearBtn = document.getElementById("rmChatClear");
    const sendBtn = document.getElementById("rmChatSend");
    const input = document.getElementById("rmChatInput");
    const quickBox = document.getElementById("rmChatQuick");
    const soundBtn = document.getElementById("rmChatSound");
    const voiceToggleBtn = document.getElementById("rmChatVoice");
    const micBtn = document.getElementById("rmMicBtn");
    const emojiToggle = document.getElementById("rmEmojiToggle");
    const emojiDrawer = document.getElementById("rmEmojiDrawer");

    if (!panel || !body || !toggle || !closeBtn || !clearBtn || !sendBtn || !input || !quickBox) {
        return;
    }

    // Chatbot States
    let soundEnabled = localStorage.getItem(soundConfigKey) !== 'false';
    let voiceEnabled = localStorage.getItem(voiceConfigKey) === 'true';
    let guidedState = 'idle'; // idle, ticket_name, ticket_phone, ticket_subject, ticket_message
    let ticketForm = { name: '', phone: '', subject: '', message: '' };

    // Standard Quick Reply Actions
    const mainQuickReplies = [
        "🍔 Browse Menu",
        "📦 Track Order",
        "🎟️ View Coupons",
        "✍️ Submit Ticket",
        "ℹ️ general FAQ"
    ];

    // Local static answers for General FAQs
    const faqReplies = {
        "delivery time": "Typical delivery time is 30 to 45 minutes depending on your area, restaurant queue, and traffic conditions.",
        "payment options": "We support dynamic UPI QR code scanning, Debit/Credit cards, cash on delivery (COD), and net banking.",
        "cancellation policy": "Orders can be cancelled directly from your account within 5 minutes of placement. For urgent help, please write to our support crew.",
        "refund policy": "Refunds are processed back to your original payment method. They usually settle in 3 to 5 business days.",
        "hours": "Our online delivery and partner kitchen networks operate daily from 9:00 AM to 11:00 PM.",
        "allergy info": "Please write allergy notes inside the checkout comments and contact the kitchen directly to verify ingredients before eating.",
        "general faq": "I can help you with:\n• Delivery Time\n• Payment Options\n• Cancellation Policy\n• Refund Policy\n• Operating Hours\n• Allergy Information"
    };

    // Initialize Audio Context for Natively Synthesized Sound Effects (No remote audio files needed)
    let audioCtx = null;
    function playSound(type) {
        if (!soundEnabled) return;
        try {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            const now = audioCtx.currentTime;
            
            if (type === 'sent') {
                // Soft upward swoosh frequency
                osc.frequency.setValueAtTime(450, now);
                osc.frequency.exponentialRampToValueAtTime(750, now + 0.12);
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.linearRampToValueAtTime(0.001, now + 0.12);
                osc.start(now);
                osc.stop(now + 0.12);
            } else if (type === 'received') {
                // Two-tone friendly chime
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, now); // C5
                osc.frequency.setValueAtTime(659.25, now + 0.08); // E5
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.setValueAtTime(0.08, now + 0.08);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc.start(now);
                osc.stop(now + 0.3);
            }
        } catch (e) {
            console.warn("Audio Context playback failed", e);
        }
    }

    // TTS Voice Narration
    function speakText(text) {
        if (!voiceEnabled || !window.speechSynthesis) return;
        try {
            // Cancel current speech
            window.speechSynthesis.cancel();
            
            // Clean text of HTML tags
            const cleanText = text.replace(/<\/?[^>]+(>|$)/g, " ");
            
            const utterance = new SpeechSynthesisUtterance(cleanText);
            utterance.rate = 1.05;
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        } catch (e) {
            console.warn("Speech Synthesis failed", e);
        }
    }

    // Initialize UI Controls from Storage settings
    function syncControlButtons() {
        if (soundEnabled) {
            soundBtn.classList.add("active-control");
            soundBtn.innerHTML = '<i class="fas fa-bell"></i>';
        } else {
            soundBtn.classList.remove("active-control");
            soundBtn.innerHTML = '<i class="fas fa-bell-slash"></i>';
        }

        if (voiceEnabled) {
            voiceToggleBtn.classList.add("active-control");
            voiceToggleBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
        } else {
            voiceToggleBtn.classList.remove("active-control");
            voiceToggleBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
        }
    }

    // Sound and Voice click events
    soundBtn.addEventListener("click", () => {
        soundEnabled = !soundEnabled;
        localStorage.setItem(soundConfigKey, soundEnabled);
        syncControlButtons();
        if (soundEnabled) playSound('received');
    });

    voiceToggleBtn.addEventListener("click", () => {
        voiceEnabled = !voiceEnabled;
        localStorage.setItem(voiceConfigKey, voiceEnabled);
        syncControlButtons();
        if (voiceEnabled) {
            speakText("Narration is now enabled");
        } else {
            if (window.speechSynthesis) window.speechSynthesis.cancel();
        }
    });

    // Emoji Box Actions
    emojiToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        emojiDrawer.classList.toggle("is-open");
    });

    document.addEventListener("click", (e) => {
        if (!emojiDrawer.contains(e.target) && e.target !== emojiToggle) {
            emojiDrawer.classList.remove("is-open");
        }
    });

    document.querySelectorAll(".rm-chatbot-emoji-item").forEach(item => {
        item.addEventListener("click", () => {
            input.value += item.textContent;
            emojiDrawer.classList.remove("is-open");
            input.focus();
        });
    });

    // Speech-to-Text (STT) Integration
    let recognition = null;
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        recognition.onstart = () => {
            micBtn.classList.add("listening");
            input.placeholder = "Listening... Speak now";
        };

        recognition.onend = () => {
            micBtn.classList.remove("listening");
            input.placeholder = "Ask about menu, offers or enter Order ID...";
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            input.value = transcript;
            sendMessage();
        };

        recognition.onerror = (event) => {
            console.error("Speech Recognition Error", event);
            micBtn.classList.remove("listening");
        };

        micBtn.addEventListener("click", () => {
            if (micBtn.classList.contains("listening")) {
                recognition.stop();
            } else {
                recognition.start();
            }
        });
    } else {
        micBtn.style.display = "none";
    }

    // Scroll chat body to bottom
    function scrollToBottom() {
        body.scrollTop = body.scrollHeight;
    }

    // Append Message UI
    function appendMessage(type, text, save = true, speak = false) {
        const msgRow = document.createElement("div");
        msgRow.className = `rm-chatbot-msg-row ${type}-row`;

        const avatar = document.createElement("div");
        avatar.className = "rm-chatbot-msg-avatar";
        avatar.innerHTML = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';
        msgRow.appendChild(avatar);

        const msgBubble = document.createElement("div");
        msgBubble.className = `rm-chatbot-msg ${type}`;
        
        // Sanitize & insert body (support line breaks and list bullets)
        const formattedText = text.replace(/\n/g, "<br>");
        msgBubble.innerHTML = formattedText;

        // If bot, add speech reader option
        if (type === 'bot') {
            const speakBtn = document.createElement("button");
            speakBtn.className = "rm-chatbot-read-speak-btn";
            speakBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            speakBtn.title = "Read aloud";
            speakBtn.addEventListener("click", () => speakText(text));
            msgBubble.appendChild(speakBtn);
        }

        const timeNode = document.createElement("span");
        timeNode.className = "rm-chatbot-msg-time";
        const date = new Date();
        timeNode.textContent = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        msgBubble.appendChild(timeNode);

        msgRow.appendChild(msgBubble);
        body.appendChild(msgRow);
        scrollToBottom();

        if (save) {
            const history = JSON.parse(sessionStorage.getItem(storageKey) || "[]");
            history.push({ type, text });
            sessionStorage.setItem(storageKey, JSON.stringify(history));
        }

        if (speak && type === 'bot') {
            speakText(text);
        }
    }

    // Render typing indicator bubble
    function showTypingIndicator() {
        const row = document.createElement("div");
        row.className = "rm-chatbot-msg-row bot-row temp-typing-bubble";
        
        const avatar = document.createElement("div");
        avatar.className = "rm-chatbot-msg-avatar";
        avatar.innerHTML = '<i class="fas fa-robot"></i>';
        row.appendChild(avatar);

        const bubble = document.createElement("div");
        bubble.className = "rm-chatbot-msg bot";
        bubble.innerHTML = '<span class="rm-chatbot-typing"><span></span><span></span><span></span></span>';
        row.appendChild(bubble);
        
        body.appendChild(row);
        scrollToBottom();
        return row;
    }

    // Setup dynamic Quick Action chips in panel
    function renderQuickReplies(repliesList) {
        quickBox.innerHTML = '';
        repliesList.forEach(label => {
            const chip = document.createElement("button");
            chip.className = "rm-chatbot-chip";
            chip.type = "button";
            chip.textContent = label;
            chip.addEventListener("click", () => {
                input.value = label;
                sendMessage();
            });
            quickBox.appendChild(chip);
        });
    }

    // AJAX add item to cart directly via site standard `manage-cart` endpoint
    window.chatbotAddToCart = function(btnElement, id, name, price, restaurant) {
        btnElement.disabled = true;
        btnElement.textContent = "Adding...";
        
        const formData = new URLSearchParams();
        formData.append('Add_To_Cart', '1');
        formData.append('Id', id);
        formData.append('Item_Name', name);
        formData.append('Price', price);
        formData.append('Restro_Name', restaurant);
        formData.append('ajax', '1');

        fetch(siteUrl + "manage-cart", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                btnElement.textContent = "Added ✓";
                btnElement.style.background = "#22c55e";
                playSound('received');
                
                // Dynamically update site header cart count if present
                const countBadge = document.querySelector(".site-header-wrap .badge, .navbar .badge, .cart-count");
                if (countBadge) {
                    let currentCount = parseInt(countBadge.textContent) || 0;
                    countBadge.textContent = currentCount + 1;
                }
            } else if (data.status === 'info') {
                btnElement.textContent = "In Cart";
                btnElement.style.background = "#3b82f6";
            } else {
                btnElement.textContent = "Error";
                btnElement.disabled = false;
            }
        })
        .catch(err => {
            console.error("Cart AJAX error", err);
            btnElement.textContent = "Failed";
            btnElement.disabled = false;
        });
    };

    // Copy coupon code to clipboard helper
    window.chatbotCopyCoupon = function (btnElement, code) {
        navigator.clipboard.writeText(code).then(() => {
            const originalText = btnElement.textContent;
            btnElement.textContent = "Copied!";
            btnElement.style.background = "#22c55e";
            btnElement.style.color = "#fff";
            playSound('received');
            setTimeout(() => {
                btnElement.textContent = originalText;
                btnElement.style.background = "#fff";
                btnElement.style.color = "#e69500";
            }, 1500);
        }).catch(err => {
            console.error("Clipboard copy failed", err);
        });
    };

    // API: Browse Categories
    function triggerBrowseCategories() {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "get_categories");

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success && data.categories.length > 0) {
                appendMessage("bot", "Please select a category to view items:", true, true);
                
                // Render custom horizontal scrolling element
                const scrollWrapper = document.createElement("div");
                scrollWrapper.className = "rm-chatbot-scroll-wrapper";
                
                data.categories.forEach(cat => {
                    const card = document.createElement("div");
                    card.className = "rm-chatbot-category-card";
                    
                    const img = document.createElement("img");
                    img.src = cat.image ? cat.image : 'images/bg_homepage.jpg';
                    img.alt = cat.title;
                    card.appendChild(img);
                    
                    const span = document.createElement("span");
                    span.textContent = cat.title;
                    card.appendChild(span);
                    
                    card.addEventListener("click", () => {
                        triggerFetchFoodsByCategory(cat.id, cat.title);
                    });
                    
                    scrollWrapper.appendChild(card);
                });
                
                body.appendChild(scrollWrapper);
                scrollToBottom();
            } else {
                appendMessage("bot", "No active menu categories were found.", true, true);
            }
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Unable to load categories. Please try again.", true, true);
        });
    }

    // API: Fetch Foods By Category
    function triggerFetchFoodsByCategory(categoryId, categoryName) {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "get_foods");
        params.append("category_id", categoryId);

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success && data.foods.length > 0) {
                appendMessage("bot", `Here are some popular picks in **${categoryName}**:`, true, false);
                
                const card = document.createElement("div");
                card.className = "rm-chatbot-rich-card";
                
                const header = document.createElement("div");
                header.className = "rm-chatbot-rich-header";
                header.innerHTML = `<span><i class="fas fa-hamburger"></i> ${categoryName} Menu</span>`;
                card.appendChild(header);

                const listBody = document.createElement("div");
                listBody.className = "rm-chatbot-rich-body";

                data.foods.forEach(food => {
                    const item = document.createElement("div");
                    item.className = "rm-chatbot-food-item";
                    
                    const img = document.createElement("img");
                    img.className = "rm-chatbot-food-img";
                    img.src = food.image ? food.image : 'images/bg_homepage.jpg';
                    img.alt = food.title;
                    item.appendChild(img);

                    const info = document.createElement("div");
                    info.className = "rm-chatbot-food-info";
                    
                    const title = document.createElement("p");
                    title.className = "rm-chatbot-food-title";
                    title.textContent = food.title;
                    info.appendChild(title);

                    const priceRow = document.createElement("div");
                    priceRow.className = "rm-chatbot-food-price-row";
                    
                    const price = document.createElement("span");
                    price.className = "rm-chatbot-food-price";
                    price.textContent = `INR ${food.price}`;
                    priceRow.appendChild(price);

                    const stockStr = food.stock > 0 ? (food.stock <= 3 ? 'Only a few left!' : 'In Stock') : 'Out of Stock';
                    const stockClass = food.stock > 0 ? (food.stock <= 3 ? 'low-stock' : 'in-stock') : 'out-stock';
                    const stock = document.createElement("span");
                    stock.className = `rm-chatbot-food-stock ${stockClass}`;
                    stock.textContent = stockStr;
                    priceRow.appendChild(stock);

                    info.appendChild(priceRow);
                    item.appendChild(info);

                    if (food.stock > 0) {
                        const addBtn = document.createElement("button");
                        addBtn.className = "rm-chatbot-add-cart-btn";
                        addBtn.type = "button";
                        addBtn.textContent = "Order";
                        addBtn.setAttribute("onclick", `chatbotAddToCart(this, ${food.id}, '${food.title.replace(/'/g, "\\'")}', ${food.price}, '${food.restaurant.replace(/'/g, "\\'")}')`);
                        item.appendChild(addBtn);
                    }

                    listBody.appendChild(item);
                });

                card.appendChild(listBody);
                body.appendChild(card);
                scrollToBottom();
                playSound('received');
            } else {
                appendMessage("bot", `No items found in category ${categoryName}.`, true, true);
            }
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Unable to load menu items. Please try again.", true, true);
        });
    }

    // API: Search Foods by Text Query
    function triggerFoodSearch(query) {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "get_foods");
        params.append("query", query);

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success && data.foods.length > 0) {
                appendMessage("bot", `I found matching items for "${query}":`, true, false);
                
                const card = document.createElement("div");
                card.className = "rm-chatbot-rich-card";
                
                const header = document.createElement("div");
                header.className = "rm-chatbot-rich-header";
                header.innerHTML = `<span><i class="fas fa-search"></i> Search Results</span>`;
                card.appendChild(header);

                const listBody = document.createElement("div");
                listBody.className = "rm-chatbot-rich-body";

                data.foods.forEach(food => {
                    const item = document.createElement("div");
                    item.className = "rm-chatbot-food-item";
                    
                    const img = document.createElement("img");
                    img.className = "rm-chatbot-food-img";
                    img.src = food.image ? food.image : 'images/bg_homepage.jpg';
                    img.alt = food.title;
                    item.appendChild(img);

                    const info = document.createElement("div");
                    info.className = "rm-chatbot-food-info";
                    
                    const title = document.createElement("p");
                    title.className = "rm-chatbot-food-title";
                    title.textContent = food.title;
                    info.appendChild(title);

                    const priceRow = document.createElement("div");
                    priceRow.className = "rm-chatbot-food-price-row";
                    
                    const price = document.createElement("span");
                    price.className = "rm-chatbot-food-price";
                    price.textContent = `INR ${food.price}`;
                    priceRow.appendChild(price);

                    const stockStr = food.stock > 0 ? (food.stock <= 3 ? 'Only a few left!' : 'In Stock') : 'Out of Stock';
                    const stockClass = food.stock > 0 ? (food.stock <= 3 ? 'low-stock' : 'in-stock') : 'out-stock';
                    const stock = document.createElement("span");
                    stock.className = `rm-chatbot-food-stock ${stockClass}`;
                    stock.textContent = stockStr;
                    priceRow.appendChild(stock);

                    info.appendChild(priceRow);
                    item.appendChild(info);

                    if (food.stock > 0) {
                        const addBtn = document.createElement("button");
                        addBtn.className = "rm-chatbot-add-cart-btn";
                        addBtn.type = "button";
                        addBtn.textContent = "Order";
                        addBtn.setAttribute("onclick", `chatbotAddToCart(this, ${food.id}, '${food.title.replace(/'/g, "\\'")}', ${food.price}, '${food.restaurant.replace(/'/g, "\\'")}')`);
                        item.appendChild(addBtn);
                    }

                    listBody.appendChild(item);
                });

                card.appendChild(listBody);
                body.appendChild(card);
                scrollToBottom();
                playSound('received');
            } else {
                appendMessage("bot", `I couldn't find any dishes matching "${query}". Try searching pizza, burger, or pasta!`, true, true);
            }
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Search operation timed out. Please try again.", true, true);
        });
    }

    // API: Fetch Active Coupons
    function triggerGetCoupons() {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "get_coupons");

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success && data.coupons.length > 0) {
                appendMessage("bot", "Active promotion offers you can copy and use at checkout:", true, false);
                
                const card = document.createElement("div");
                card.className = "rm-chatbot-rich-card";
                
                const header = document.createElement("div");
                header.className = "rm-chatbot-rich-header";
                header.innerHTML = '<span><i class="fas fa-tags"></i> Coupon Deals</span>';
                card.appendChild(header);

                const listBody = document.createElement("div");
                listBody.className = "rm-chatbot-rich-body";

                data.coupons.forEach(coupon => {
                    const cRow = document.createElement("div");
                    cRow.className = "rm-chatbot-coupon-card";

                    const details = document.createElement("div");
                    details.className = "rm-chatbot-coupon-details";
                    
                    const code = document.createElement("span");
                    code.className = "rm-chatbot-coupon-code";
                    code.textContent = coupon.code;
                    details.appendChild(code);

                    const name = document.createElement("p");
                    name.className = "rm-chatbot-coupon-name";
                    name.textContent = `${coupon.name} (${coupon.type})`;
                    details.appendChild(name);
                    
                    cRow.appendChild(details);

                    const disc = document.createElement("span");
                    disc.className = "rm-chatbot-coupon-disc";
                    disc.textContent = `₹${coupon.discount} Off`;
                    cRow.appendChild(disc);

                    const cpBtn = document.createElement("button");
                    cpBtn.className = "rm-chatbot-coupon-copy";
                    cpBtn.type = "button";
                    cpBtn.textContent = "Copy";
                    cpBtn.setAttribute("onclick", `chatbotCopyCoupon(this, '${coupon.code}')`);
                    cRow.appendChild(cpBtn);

                    listBody.appendChild(cRow);
                });

                card.appendChild(listBody);
                body.appendChild(card);
                scrollToBottom();
                playSound('received');
            } else {
                appendMessage("bot", "There are no active coupons at the moment. Check back during festivals!", true, true);
            }
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Failed to retrieve coupons. Please try again shortly.", true, true);
        });
    }

    // API: Track Order Status
    function triggerTrackOrder(orderId) {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "track_order");
        params.append("order_id", orderId);

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success && data.order) {
                const order = data.order;
                
                let orderItemsHtml = '';
                if (order.items && order.items.length > 0) {
                    orderItemsHtml = '<div style="margin-top:8px; border-top: 1px solid #f1f5f9; padding-top:6px; font-size:0.8rem;">';
                    order.items.forEach(item => {
                        orderItemsHtml += `• ${item.name} x ${item.quantity} (₹${item.price})<br>`;
                    });
                    orderItemsHtml += '</div>';
                }

                const summaryText = `
                    📦 **Order #${order.order_id} Summary**
                    **Customer**: ${order.customer_name}
                    **Address**: ${order.address}
                    **Total Bill**: INR ${order.total_amount}
                    **Payment Status**: ${order.payment_status}
                    **Rider Assigned**: ${order.delivery_boy}
                    ${orderItemsHtml}
                `;
                appendMessage("bot", summaryText, true, false);

                // Build Visual Stepper Timeline
                const card = document.createElement("div");
                card.className = "rm-chatbot-rich-card";
                
                const header = document.createElement("div");
                header.className = "rm-chatbot-rich-header";
                header.innerHTML = '<span><i class="fas fa-shipping-fast"></i> Order Progress</span>';
                card.appendChild(header);

                const trackerBody = document.createElement("div");
                trackerBody.className = "rm-chatbot-rich-body rm-chatbot-tracker";

                const states = [
                    { key: "Pending", title: "Order Placed", desc: "Waiting for restaurant approval" },
                    { key: "Processing", title: "Preparing Food", desc: "Kitchen is preparing your fresh meal" },
                    { key: "On The Way", title: "On the Way", desc: "Rider is delivering your packet" },
                    { key: "Delivered", title: "Delivered", desc: "Order completed. Bon appétit!" }
                ];

                const currentStatus = order.order_status;
                let activeIndex = 0;
                
                if (currentStatus === "Processing") activeIndex = 1;
                else if (currentStatus === "On The Way" || currentStatus === "On the Way") activeIndex = 2;
                else if (currentStatus === "Delivered") activeIndex = 3;
                else if (currentStatus === "Cancelled") activeIndex = -1; // special case

                if (currentStatus === "Cancelled") {
                    trackerBody.innerHTML = `
                        <div style="text-align:center; padding: 10px; color:#ef4444;">
                            <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                            <p style="font-weight:700; margin-top:8px;">Order Cancelled</p>
                            <span style="font-size:0.75rem; color:var(--chat-text-muted);">This order has been cancelled and refund initiated.</span>
                        </div>
                    `;
                } else {
                    states.forEach((s, idx) => {
                        const step = document.createElement("div");
                        step.className = "rm-chatbot-tracker-step";
                        
                        if (idx < activeIndex) {
                            step.classList.add("completed");
                        } else if (idx === activeIndex) {
                            step.classList.add("active");
                        }

                        const node = document.createElement("div");
                        node.className = "rm-chatbot-tracker-node";
                        node.innerHTML = idx < activeIndex ? '<i class="fas fa-check"></i>' : (idx + 1);
                        step.appendChild(node);

                        const info = document.createElement("div");
                        info.className = "rm-chatbot-tracker-info";
                        
                        const title = document.createElement("p");
                        title.className = "rm-chatbot-tracker-title";
                        title.textContent = s.title;
                        info.appendChild(title);

                        const desc = document.createElement("span");
                        desc.className = "rm-chatbot-tracker-desc";
                        desc.textContent = s.desc;
                        info.appendChild(desc);

                        step.appendChild(info);
                        trackerBody.appendChild(step);
                    });
                }

                card.appendChild(trackerBody);
                body.appendChild(card);
                scrollToBottom();
                playSound('received');
                speakText(`Your order status is ${currentStatus}`);
            } else {
                appendMessage("bot", `I couldn't find any order with ID "${orderId}". Please make sure the number is correct.`, true, true);
            }
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Tracking lookup failed. Please try again soon.", true, true);
        });
    }

    // API: Submit Inquiry Ticket
    function triggerSubmitTicket(formData) {
        const loader = showTypingIndicator();
        
        const params = new URLSearchParams();
        params.append("action", "submit_inquiry");
        params.append("name", formData.name);
        params.append("phone", formData.phone);
        params.append("subject", formData.subject);
        params.append("message", formData.message);

        fetch(siteUrl + "chatbot_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            if (data.success) {
                appendMessage("bot", `🎉 **Ticket Created Successfully!**\n\n${data.message}`, true, true);
            } else {
                appendMessage("bot", `⚠️ **Failed to create ticket:**\n${data.message}`, true, true);
            }
            guidedState = 'idle';
            renderQuickReplies(mainQuickReplies);
        })
        .catch(err => {
            loader.remove();
            appendMessage("bot", "Network error occurred while submitting. Please try again.", true, true);
            guidedState = 'idle';
            renderQuickReplies(mainQuickReplies);
        });
    }

    // Message input parser / NLP engine
    function buildReply(message) {
        const clean = message.trim();
        const normalized = clean.toLowerCase();

        // 1. Guided Conversational Ticket Forms
        if (guidedState !== 'idle') {
            if (normalized === 'cancel' || normalized === 'cancel inquiry') {
                guidedState = 'idle';
                appendMessage("bot", "Support inquiry cancelled. How else can I help you?", true, true);
                renderQuickReplies(mainQuickReplies);
                return;
            }

            if (guidedState === 'ticket_name') {
                ticketForm.name = clean;
                guidedState = 'ticket_phone';
                appendMessage("bot", "Got it. Next, please enter a valid **Phone Number** where our customer executive can reach you:", true, true);
                renderQuickReplies(["Cancel Inquiry"]);
                return;
            }

            if (guidedState === 'ticket_phone') {
                const numericPhone = clean.replace(/[^0-9]/g, '');
                if (numericPhone.length < 10) {
                    appendMessage("bot", "⚠️ Please enter a valid 10-digit phone number. Let's try again:", true, true);
                    return;
                }
                ticketForm.phone = numericPhone;
                guidedState = 'ticket_subject';
                appendMessage("bot", "Thank you. What is the **Subject** of your inquiry? (e.g. Late delivery, Bad packaging, Payment failed)", true, true);
                renderQuickReplies(["Cancel Inquiry"]);
                return;
            }

            if (guidedState === 'ticket_subject') {
                ticketForm.subject = clean;
                guidedState = 'ticket_message';
                appendMessage("bot", "Perfect. Now write your **Detailed Message** describing the issue:", true, true);
                renderQuickReplies(["Cancel Inquiry"]);
                return;
            }

            if (guidedState === 'ticket_message') {
                ticketForm.message = clean;
                guidedState = 'ticket_submitting';
                appendMessage("bot", "Submitting your support inquiry. Please wait...", true, false);
                triggerSubmitTicket(ticketForm);
                return;
            }
            return;
        }

        // 2. Direct Commands / Quick replies
        if (normalized.includes("browse menu") || normalized === "menu") {
            triggerBrowseCategories();
            return;
        }

        if (normalized.includes("view coupons") || normalized === "offers" || normalized === "coupons") {
            triggerGetCoupons();
            return;
        }

        if (normalized.includes("track order") || normalized === "order status") {
            appendMessage("bot", "Please type your **Order ID** number to track its current progress:", true, true);
            renderQuickReplies(["Cancel"]);
            return;
        }

        if (normalized.includes("submit ticket") || normalized === "support" || normalized === "ticket" || normalized === "contact") {
            guidedState = 'ticket_name';
            appendMessage("bot", "Let's create a customer support ticket. First, please enter your **Full Name**:", true, true);
            renderQuickReplies(["Cancel Inquiry"]);
            return;
        }

        // 3. Numeric matches for Order ID tracking
        if (/^\d+$/.test(normalized)) {
            triggerTrackOrder(normalized);
            return;
        }

        // 4. Greetings
        if (normalized === 'hello' || normalized === 'hi' || normalized === 'hey') {
            appendMessage("bot", "Hello! How can I assist you with your hunger today?", true, true);
            return;
        }
        if (normalized === 'bye' || normalized === 'goodbye' || normalized === 'thank you' || normalized === 'thanks') {
            appendMessage("bot", "You are welcome! Have a wonderful day. Bon appétit!", true, true);
            return;
        }

        // 5. General FAQs
        for (const key in faqReplies) {
            if (normalized.includes(key)) {
                appendMessage("bot", faqReplies[key], true, true);
                return;
            }
        }

        // 6. Food item search parser (if none of the above matches)
        if (clean.length > 2) {
            triggerFoodSearch(clean);
            return;
        }

        appendMessage("bot", "I didn't quite catch that. Try clicking a quick response button or type 'help' for options.", true, true);
    }

    // Submit message handling
    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        appendMessage("user", message, true);
        input.value = "";
        playSound('sent');

        const loader = showTypingIndicator();
        setTimeout(() => {
            loader.remove();
            buildReply(message);
        }, 600);
    }

    // Setup history memory rendering
    function renderHistory() {
        const history = JSON.parse(sessionStorage.getItem(storageKey) || "[]");
        if (history.length === 0) {
            appendMessage("bot", "Hi! I am your Pasar Kita support helper. Select a service below or type your questions.", false, false);
            renderQuickReplies(mainQuickReplies);
            return;
        }

        history.forEach(item => appendMessage(item.type, item.text, false, false));
        if (guidedState === 'idle') {
            renderQuickReplies(mainQuickReplies);
        } else {
            renderQuickReplies(["Cancel Inquiry"]);
        }
    }

    // Panel controls
    function openPanel() {
        panel.classList.add("is-open");
        toggle.style.display = "none";
        playSound('received');
        setTimeout(() => input.focus(), 80);
    }

    // Close panel
    function closePanel() {
        panel.classList.remove("is-open");
        toggle.style.display = "inline-flex";
        if (window.speechSynthesis) window.speechSynthesis.cancel();
    }

    // Keyboard events
    input.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            sendMessage();
        }
    });

    // Control triggers
    toggle.addEventListener("click", openPanel);
    closeBtn.addEventListener("click", closePanel);
    sendBtn.addEventListener("click", sendMessage);

    clearBtn.addEventListener("click", () => {
        sessionStorage.removeItem(storageKey);
        body.innerHTML = "";
        guidedState = 'idle';
        if (window.speechSynthesis) window.speechSynthesis.cancel();
        appendMessage("bot", "Conversation history cleared. How can I help you now?", false, true);
        renderQuickReplies(mainQuickReplies);
    });

    // Initial setup runs
    syncControlButtons();
    renderHistory();
})();
</script>
