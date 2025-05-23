<!-- Nút mở form chat nổi -->
<button onclick="toggleChatForm()" title="Liên hệ" style="
  position: fixed; bottom: 20px; right: 20px;
  width: 60px; height: 60px; border-radius: 50%;
  background-color: #007bff; color: white; font-size: 24px;
  border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 9999;
">
  💬
</button>

<!-- Form liên hệ nổi -->
<div id="formLienHe" style="
  display: none;
  position: fixed; bottom: 90px; right: 20px;
  width: 320px; background: white;
  border: 1px solid #ccc; border-radius: 10px;
  box-shadow: 0 0 12px rgba(0,0,0,0.3);
  z-index: 9999;
">
  <div style="background: #007bff; color: white; padding: 10px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
    ✉️ Liên hệ với Thư viện
    <span onclick="toggleChatForm()" style="float: right; cursor: pointer;">×</span>
  </div>
  <form onsubmit="submitLienHe(event)" style="padding: 10px;">
    <input type="text" placeholder="Họ tên" class="form-control mb-2" required>
    <input type="tel" placeholder="Số điện thoại" class="form-control mb-2" required>
    <input type="email" placeholder="Gmail" class="form-control mb-2" required>
    <textarea placeholder="Nội dung liên hệ" class="form-control mb-2" rows="3" required></textarea>
    <button type="submit" class="btn btn-primary w-100">Gửi</button>
  </form>
</div>

<script>
  function toggleChatForm() {
    const form = document.getElementById('formLienHe');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
  }

  function submitLienHe(e) {
    e.preventDefault();
    alert("✅ Thư viện đã nhận được liên hệ của bạn!");
    document.getElementById('formLienHe').style.display = 'none';
    e.target.reset();
  }
</script>
