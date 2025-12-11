// Viewer sederhana untuk PDF + download
document.addEventListener('DOMContentLoaded', () => {
  // tutup modal ketika klik di luar
  const modal = document.getElementById('pdfModal');
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) closePDFModal();
  });
  document.addEventListener('keydown', (e)=>{
    if(e.key==='Escape') closePDFModal();
  });
});

function viewPDF(pdfPath) {
  const modal = document.getElementById('pdfModal');
  const viewer = document.getElementById('pdfViewer');
  const title = document.getElementById('pdfTitle');

  const filename = (pdfPath.split('/').pop() || '').replace(/\.(pdf)$/i,'');
  title.textContent = filename.replace(/[-_]/g,' ').toUpperCase();

  viewer.src = pdfPath;
  modal.style.display = 'block';
  requestAnimationFrame(()=> modal.style.opacity = '1');
}

function closePDFModal() {
  const modal = document.getElementById('pdfModal');
  const viewer = document.getElementById('pdfViewer');
  modal.style.opacity = '0';
  setTimeout(()=> { modal.style.display = 'none'; viewer.src = ''; }, 200);
}

function downloadPDF(path, filename) {
  const a = document.createElement('a');
  a.href = path;
  a.download = filename || '';
  document.body.appendChild(a);
  a.click();
  a.remove();
}