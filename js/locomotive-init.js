import LocomotiveScroll from 'locomotive-scroll';

document.addEventListener('DOMContentLoaded', function() {
  const scroll = new LocomotiveScroll({
    el: document.querySelector('[data-scroll-container]'),
    smooth: true,
    smartphone: {
      smooth: true
    },
    tablet: {
      smooth: true
    },
    reloadOnContext: true,
    multiplier: 1,
    getSpeed: true,
    getDirection: true
  });

  scroll.on('scroll', (instance) => {
    document.dispatchEvent(new CustomEvent('scroll', { detail: instance }));
  });
});
