<?php
/**
 * TGR Soft credit badge — sağ-altta sabit, diskret bir "Powered by" rozeti.
 * Her panelde (frontend / admin / customer / reset-password) footer öncesinde
 * çağrılır. Konum: position:fixed, sağ-alt.
 *
 * Üzerine gelindiğinde genişler ve TGR Soft logosunu/ismini açar.
 */
?>
<a class="tgr-badge"
   href="https://tgrsoft.com"
   target="_blank"
   rel="noopener sponsored"
   title="TGR Soft — Yazılım & Dijital Çözümler"
   aria-label="Powered by TGR Soft">
    <span class="tgr-badge__dot" aria-hidden="true">
        <i class="bi bi-lightning-charge-fill"></i>
    </span>
    <span class="tgr-badge__text">
        <small>Powered by</small>
        <strong>TGR <span>Soft</span></strong>
    </span>
</a>
