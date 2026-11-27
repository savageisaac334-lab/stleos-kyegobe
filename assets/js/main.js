(function () {
  'use strict';
  // mobile navigation
  var tog = document.querySelector('.nav-toggle'), nav = document.getElementById('mainnav');
  if (tog && nav) {
    tog.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      tog.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // hero slider
  var hero = document.querySelector('.hero');
  if (hero) {
    var slides = hero.querySelectorAll('.slide'), dots = hero.querySelectorAll('.dot'), idx = 0, timer;
    var show = function (n) {
      idx = (n + slides.length) % slides.length;
      slides.forEach(function (s, i) { s.classList.toggle('is-active', i === idx); });
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
    };
    var start = function () { stop(); timer = setInterval(function () { show(idx + 1); }, 6500); };
    var stop = function () { if (timer) { clearInterval(timer); } };
    hero.querySelector('.prev').addEventListener('click', function () { show(idx - 1); start(); });
    hero.querySelector('.next').addEventListener('click', function () { show(idx + 1); start(); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { show(i); start(); }); });
    hero.addEventListener('mouseenter', stop); hero.addEventListener('mouseleave', start);
    start();
  }

  // gallery filter + lightbox
  var grid = document.getElementById('mgrid');
  if (grid) {
    var tiles = Array.prototype.slice.call(grid.querySelectorAll('.mtile'));
    document.querySelectorAll('.filter[data-filter]').forEach(function (b) {
      b.addEventListener('click', function () {
        document.querySelectorAll('.filter').forEach(function (x) { x.classList.remove('is-active'); });
        b.classList.add('is-active');
        var f = b.getAttribute('data-filter');
        tiles.forEach(function (t) { t.hidden = !(f === 'all' || t.getAttribute('data-cat') === f); });
      });
    });
    var lb = document.getElementById('lightbox'), cur = 0, list = [];
    var img = lb.querySelector('img'), cap = lb.querySelector('figcaption');
    var render = function () {
      var a = list[cur];
      img.src = a.getAttribute('href'); img.alt = a.getAttribute('data-title');
      cap.innerHTML = '';
      var s = document.createElement('strong'); s.textContent = a.getAttribute('data-title');
      var p = document.createElement('span'); p.textContent = a.getAttribute('data-desc');
      cap.appendChild(s); cap.appendChild(p);
    };
    var open = function (i) {
      list = tiles.filter(function (t) { return !t.hidden; }).map(function (t) { return t.querySelector('a'); });
      cur = i; render(); lb.hidden = false; document.body.style.overflow = 'hidden';
    };
    var close = function () { lb.hidden = true; document.body.style.overflow = ''; };
    grid.addEventListener('click', function (ev) {
      var a = ev.target.closest('a.mlink'); if (!a) { return; }
      ev.preventDefault();
      var vis = tiles.filter(function (t) { return !t.hidden; }).map(function (t) { return t.querySelector('a'); });
      open(vis.indexOf(a));
    });
    lb.querySelector('.lb-close').addEventListener('click', close);
    lb.querySelector('.lb-prev').addEventListener('click', function () { cur = (cur - 1 + list.length) % list.length; render(); });
    lb.querySelector('.lb-next').addEventListener('click', function () { cur = (cur + 1) % list.length; render(); });
    lb.addEventListener('click', function (e) { if (e.target === lb) { close(); } });
    document.addEventListener('keydown', function (e) {
      if (lb.hidden) { return; }
      if (e.key === 'Escape') { close(); }
      if (e.key === 'ArrowLeft') { cur = (cur - 1 + list.length) % list.length; render(); }
      if (e.key === 'ArrowRight') { cur = (cur + 1) % list.length; render(); }
    });
  }
})();
