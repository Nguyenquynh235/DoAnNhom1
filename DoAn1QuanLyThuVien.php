<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Thư Viện</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">Quản Lý Thư Viện</h1>

        <!-- Form thêm sách -->
        123 Quynh
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Thêm Sách</h2>
            <input id="title" type="text" placeholder="Tên sách" class="border p-2 rounded w-full mb-2">
            <input id="author" type="text" placeholder="Tác giả" class="border p-2 rounded w-full mb-2">
            <input id="year" type="number" placeholder="Năm xuất bản" class="border p-2 rounded w-full mb-2">
            <button onclick="addBook()" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Thêm Sách</button>
        </div>

        <!-- Ô tìm kiếm -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Tìm Kiếm Sách</h2>
            <input id="search" type="text" placeholder="Nhập tên sách hoặc tác giả" class="border p-2 rounded w-full mb-2" oninput="searchBook()">
        </div>

        <!-- Danh sách sách -->
        <h2 class="text-xl font-semibold mb-4">Danh Sách Sách</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Tên Sách</th>
                    <th class="border p-2">Tác Giả</th>
                    <th class="border p-2">Năm XB</th>
                    <th class="border p-2">Hành Động</th>
                </tr>
            </thead>
            <tbody id="bookList"></tbody>
        </table>
    </div>

    <script>
        let books = [];

        function addBook() {
            const title = document.getElementById('title').value;
            const author = document.getElementById('author').value;
            const year = document.getElementById('year').value;

            if (!title || !author || !year) {
                alert('Vui lòng nhập đầy đủ thông tin!');
                return;
            }

            books.push({ title, author, year });
            displayBooks();
            clearForm();
        }

        function displayBooks(filteredBooks = books) {
            const bookList = document.getElementById('bookList');
            bookList.innerHTML = '';
            filteredBooks.forEach((book, index) => {
                bookList.innerHTML += `
                    <tr>
                        <td class="border p-2">${book.title}</td>
                        <td class="border p-2">${book.author}</td>
                        <td class="border p-2">${book.year}</td>
                        <td class="border p-2">
                            <button onclick="editBook(${index})" class="text-yellow-500">Sửa</button> |
                            <button onclick="deleteBook(${index})" class="text-red-500">Xóa</button>
                        </td>
                    </tr>`;
            });
        }

        function clearForm() {
            document.getElementById('title').value = '';
            document.getElementById('author').value = '';
            document.getElementById('year').value = '';
        }

        function deleteBook(index) {
            if (confirm('Bạn có chắc chắn muốn xóa sách này?')) {
                books.splice(index, 1);
                displayBooks();
            }
        }

        function editBook(index) {
            const book = books[index];
            document.getElementById('title').value = book.title;
            document.getElementById('author').value = book.author;
            document.getElementById('year').value = book.year;
            deleteBook(index);
        }

        function searchBook() {
            const query = document.getElementById('search').value.toLowerCase();
            const filteredBooks = books.filter(book =>
                book.title.toLowerCase().includes(query) ||
                book.author.toLowerCase().includes(query)
            );
            displayBooks(filteredBooks);
        }
    </script>
</body>
</html>
