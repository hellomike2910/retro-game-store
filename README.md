# Retro Game Store – bản đã sửa

## Chạy bằng Visual Studio Code

1. Chọn **File → Open Folder…**, mở thư mục `Documents/retro-game-store`.
2. Nhấn **F5**, chọn **Retro Game Store – chạy website** nếu được hỏi.
3. VS Code tự khởi động PHP và mở trình duyệt. Không dùng Live Server cho file PHP.

Cần Chrome cho cấu hình F5. Nếu chưa có Chrome, dùng cách Terminal bên dưới rồi mở địa chỉ bằng trình duyệt đang có. Đóng cửa sổ gỡ lỗi không tự dừng máy chủ: trong Terminal chạy PHP, nhấn Ctrl+C để dừng.

Nếu máy chủ của chính dự án đã chạy, lệnh khởi động sẽ báo website sẵn sàng. Nếu cổng 8080 thuộc chương trình khác, lệnh sẽ báo rõ cách đổi cổng.

## Chạy trên máy Mac hiện tại

1. Mở thư mục `retro-game-store` và chạy `start.command` (nhấp đúp trong Finder).
2. Mở **http://127.0.0.1:8080**.
3. Nhấn **Ctrl+C** trong cửa sổ chạy để dừng.

File khởi động tự tìm PHP, bao gồm PHP sẵn có trong XAMPP trên máy này. Không cần bật MySQL hoặc nhập file SQL. Không mở file `.php` bằng cách kéo trực tiếp vào trình duyệt; cần chạy máy chủ như trên.

Nếu macOS không cho mở bằng nhấp đúp, mở Terminal trong thư mục `retro-game-store` rồi chạy `sh start.command`.

Windows: chạy `start.bat`; mặc định tìm PHP trên PATH hoặc `C:\xampp\php\php.exe`. Phần khởi động Windows chưa được chạy thử trên máy Windows.

Yêu cầu: PHP 8.1 trở lên, PDO và PDO SQLite. Đã kiểm tra bằng PHP 8.2.4 có sẵn trong XAMPP trên máy Mac này. Giao diện dùng các file ảnh, CSS và JavaScript trong thư mục, không cần CDN.

## Chức năng

- Menu và giao diện dùng được trên điện thoại; sửa đường dẫn ảnh phân biệt hoa thường.
- Danh mục 8 sản phẩm, tìm kiếm, lọc, chi tiết, ảnh minh họa Mario có thể bấm để đổi ảnh.
- Thêm giỏ, cập nhật số lượng, xóa; tổng tiền lấy từ danh mục ở phía máy chủ.
- Đăng ký, đăng nhập, đăng xuất, đổi mật khẩu, xem đơn hàng của chính tài khoản.
- Nhập địa chỉ để lưu đơn thử. Gửi lại biểu mẫu không tạo đơn trùng.
- Lưu lời nhắn liên hệ vào SQLite và hiện thông báo kết quả.

Tự đăng ký tài khoản mới để dùng thử. Không có tài khoản hoặc mật khẩu mặc định. Giá USD được lấy từ hình ảnh mẫu có sẵn trong bài; không đại diện giá bán hiện tại.

## Dữ liệu

SQLite tự tạo file `shop.sqlite` và các bảng `users`, `orders`, `order_items`, `messages` khi cần. Mật khẩu được lưu dưới dạng băm. Phiên đăng nhập nằm trong thư mục `sessions` riêng để tránh lỗi quyền ghi của XAMPP.

Mặc định dữ liệu nằm **ngoài thư mục website**, trong thư mục tạm của hệ thống có tên `bthk2-<mã thư mục dự án>`. Dữ liệu còn qua các lần tải trang và khởi động lại máy chủ, nhưng có thể mất khi hệ thống dọn thư mục tạm. Mã lưu trữ trong `includes/storage-id.php` giữ nguyên vị trí dữ liệu khi đổi tên thư mục dự án; không sửa hoặc xóa file này nếu muốn giữ dữ liệu cũ.

Muốn giữ lâu dài, đặt biến `BTHK2_DATA_DIR` trỏ đến một thư mục riêng có quyền ghi, nằm ngoài thư mục website. Ví dụ trên Mac, chạy trong Terminal:

```sh
BTHK2_DATA_DIR="$PWD/../retro-game-store-data" sh start.command
```

Đổi thư mục dữ liệu không tự chuyển tài khoản/đơn cũ. Muốn chuyển, dừng máy chủ và sao chép `shop.sqlite` từ vị trí cũ sang vị trí mới trước khi chạy lại. Sao lưu file này để giữ tài khoản, đơn hàng và lời nhắn; không công khai thư mục dữ liệu.

Có thể đổi cổng khi 8080 đã được sử dụng: `PORT=8081 sh start.command`, sau đó mở http://127.0.0.1:8081.

## Phạm vi bản chạy thử

Đây là ứng dụng thực hành chạy cục bộ. Đặt hàng chỉ lưu dữ liệu trong ứng dụng; chưa có cổng thanh toán, gửi email, kết nối giao hàng hay trang quản trị. Lời nhắn được lưu trong bảng `messages`, chưa gửi đến địa chỉ email của cửa hàng.

## Các file chính

- `includes/app.php`: dữ liệu, phiên đăng nhập, hàm dùng chung.
- `includes/products.php`: tên, giá và ảnh sản phẩm mẫu; chỉnh danh mục tại đây.
- `actions.php`: xử lý các biểu mẫu.
- `includes/header.php`, `includes/footer.php`: menu và chân trang dùng chung.
- `css/style.css`, `js/app.js`, `js/single_product.js`: giao diện và tương tác.
- `start.command`, `start.bat`, `router.php`: khởi động máy chủ thử và chặn truy cập file nội bộ.

Đã kiểm tra cú pháp PHP và các luồng tài khoản, giỏ hàng, đặt hàng, chống gửi trùng, lưu lời nhắn, liên kết và hình ảnh. Bộ kiểm tra dùng cơ sở dữ liệu riêng, không được đóng gói kèm tài khoản thử.
