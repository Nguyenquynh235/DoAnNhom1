<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Thư Viện</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 min-h-screen p-8">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-3xl shadow-2xl">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-gray-800">📚 Quản Lý Thư Viện 📚</h1>

        <!-- Form thêm sách -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-700">➕ Thêm Sách</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input id="title" type="text" placeholder="Tên sách" class="border p-3 rounded-xl w-full">
                <input id="author" type="text" placeholder="Tác giả" class="border p-3 rounded-xl w-full">
                <input id="year" type="number" placeholder="Năm xuất bản" class="border p-3 rounded-xl w-full">
                <input id="quantity" type="number" placeholder="Số lượng" class="border p-3 rounded-xl w-full">
            </div>
            <button onclick="addBook()" class="mt-6 bg-indigo-600 text-white p-3 rounded-xl w-full">📖 Thêm Sách</button>
        </div>

        <!-- Form mượn sách -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-700">📌 Mượn Sách</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input id="readerId" type="text" placeholder="Mã bạn đọc" class="border p-3 rounded-xl w-full">
                <input id="readerName" type="text" placeholder="Tên bạn đọc" class="border p-3 rounded-xl w-full">
                <input id="phone" type="text" placeholder="Số điện thoại" class="border p-3 rounded-xl w-full">
                <input id="email" type="text" placeholder="Email" class="border p-3 rounded-xl w-full">
                <input id="borrowTitle" type="text" placeholder="Tên sách muốn mượn" class="border p-3 rounded-xl w-full">
                <input id="borrowQuantity" type="number" placeholder="Số lượng mượn" class="border p-3 rounded-xl w-full">
            </div>
            <button onclick="borrowBook()" class="mt-6 bg-green-600 text-white p-3 rounded-xl w-full">📚 Mượn Sách</button>
        </div>


        <!-- Danh sách sách -->
        <h2 class="text-2xl font-semibold mb-6 text-gray-700">📋 Danh Sách Sách</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-xl shadow-lg">
                <thead>
                    <tr class="bg-indigo-500 text-white">
                        <th class="p-4">📕 Tên Sách</th>
                        <th class="p-4">✍️ Tác Giả</th>
                        <th class="p-4">📅 Năm XB</th>
                        <th class="p-4">📚 Số Lượng Còn</th>
                        <th class="p-4">📖 Trạng Thái</th>
                        <th class="p-4">👤 Người Mượn</th>
                        <th class="p-4">📅 Ngày Mượn</th>
                        <th class="p-4">📅 Ngày Trả</th>
                        <th class="p-4">⏳ Còn Lại</th>
                        <th class="p-4">⚙️ Hành Động</th>
                    </tr>
                </thead>
                <tbody id="bookList" class="text-gray-800"></tbody>
            </table>
        </div>
    </div>

    <script>
        let books = [];

        // Thêm sách
        function addBook() {
            const title = document.getElementById('title').value.trim();
            const author = document.getElementById('author').value.trim();
            const year = document.getElementById('year').value.trim();
            const quantity = parseInt(document.getElementById('quantity').value.trim());

            if (!title || !author || !year || isNaN(quantity) || quantity <= 0) {
                alert('⚠️ Vui lòng nhập đầy đủ thông tin hợp lệ!');
                return;
            }

            books.push({ title, author, year, quantity, borrower: '', status: 'Chưa mượn', borrowDate: '', returnDate: '', daysLeft: '' });
            displayBooks();
        }

        // Mượn sách
        function borrowBook() {
            const borrowTitle = document.getElementById('borrowTitle').value.trim();
            const borrowQuantity = parseInt(document.getElementById('borrowQuantity').value.trim());
            const readerName = document.getElementById('readerName').value.trim();

            const book = books.find(b => b.title.toLowerCase() === borrowTitle.toLowerCase());

            if (!book || book.quantity < borrowQuantity) {
                alert('⚠️ Không tìm thấy sách hoặc số lượng không đủ!');
                return;
            }

            book.quantity -= borrowQuantity;
            book.status = 'Đã mượn';
            book.borrower = readerName, readerId, phone, email;
            const today = new Date();
            book.borrowDate = today.toISOString().split('T')[0];
            const returnDate = new Date(today.setMonth(today.getMonth() + 1));
            book.returnDate = returnDate.toISOString().split('T')[0];
            book.daysLeft = Math.floor((returnDate - new Date()) / (1000 * 60 * 60 * 24));

            displayBooks();
        }

        // Hiển thị danh sách sách
        function displayBooks() {
            const bookList = document.getElementById('bookList');
            bookList.innerHTML = books.map((book, index) => `
                <tr>
                    <td>${book.title}</td>
                    <td>${book.author}</td>
                    <td>${book.year}</td>
                    <td>${book.quantity}</td>
                    <td>${book.status}</td>
                    <td>${book.borrower || '—'}</td>
                    <td>${book.borrowDate || '—'}</td>
                    <td>${book.returnDate || '—'}</td>
                    <td>${book.daysLeft || '—'}</td>
                    <td>
                        <button onclick="editBook(${index})" class="text-blue-600">✏️ Sửa</button>
                        <button onclick="deleteBook(${index})" class="text-red-600">🗑️ Xóa</button>
                        <button onclick="remind(${index})" class="text-green-600">🔔 Nhắc nhở</button>
                    </td>
                </tr>
            `).join('');
        }

        // Sửa thông tin sách
        function editBook(index) {
            const book = books[index];
            const newTitle = prompt('Nhập tên sách mới:', book.title);
            if (newTitle) books[index].title = newTitle;
            displayBooks();
        }

        // Xóa sách
        function deleteBook(index) {
            books.splice(index, 1);
            displayBooks();
        }

        // Nhắc nhở trả sách
        function remind(index) {
            const book = books[index];
            if (book.status === 'Đã mượn') {
                alert(`📢 Nhắc nhở: ${book.borrower} cần trả sách "${book.title}" trước ngày ${book.returnDate}.`);
            } else {
                alert('✅ Sách chưa được mượn.');
            }
        }
    </script>
</body>

</html>
