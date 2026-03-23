# Hướng dẫn chạy ứng dụng PHP và mở trên trình duyệt

Dự án này sử dụng PHP để chạy ứng dụng web cục bộ. Dưới đây là các cách để mở web từ file PHP.

## Yêu cầu hệ thống

- Đã cài đặt [PHP](https://www.php.net/downloads) (phiên bản 7.4 trở lên)
- Đã cài đặt web server (Apache, Nginx) hoặc sử dụng server tích hợp của PHP

## Cách 1: Sử dụng PHP Built-in Server (Đơn giản nhất)

1. Mở ```terminal``` (Command Prompt trên Windows, Terminal trên macOS/Linux).
2. Di chuyển đến thư mục chứa dự án:
   ```bash
   cd /đường/dẫn/đến/thư/mục/dự/án
3. Chạy lệnh sau để khởi động server:
    ```bash
   php -S localhost:8000
   ```
4. Mở trình duyệt và truy cập:
    ```
   http://localhost:8000
   ```
   Nếu bạn muốn mở một file cụ thể, ví dụ ```index.php```, hãy truy cập:
    ```
    http://localhost:8000/index.php
   ```

## Cách 2: Sử dụng XAMPP / WAMP / MAMP

1. Cài đặt và khởi động ```XAMPP``` (hoặc ```WAMP```, ```MAMP```).

2. Đặt thư mục dự án vào thư mục ```htdocs``` (đối với ```XAMPP```) hoặc thư mục tương ứng.

   Ví dụ với ```XAMPP```:
    ```
    C:\xampp\htdocs\ten-du-an
    ```

3. Khởi động ```Apache``` từ ```control panel``` của ```XAMPP```.

4. Mở trình duyệt và truy cập:
    ```
   http://localhost/ten-du-an
   ```
   Để mở file cụ thể:
    ```
   http://localhost/ten-du-an/index.php
   ```

## Cách 3: Sử dụng ```Docker``` (Nếu đã cài ```Docker```)

1. Tạo file ```Dockerfile``` đơn giản:
   ```
   FROM php:8.2-apache
   COPY . /var/www/html/
    ```
2. Chạy lệnh ```build``` và ```run```:
    ```
   docker build -t php-app .
    docker run -p 8080:80 php-app
    ```
3. Mở trình duyệt và truy cập:
    ```
   http://localhost:8080
   ```

## Lưu ý

1. Đảm bảo file PHP của bạn không có lỗi cú pháp.

2. Nếu cổng 8000 hoặc 8080 đã được sử dụng, hãy đổi sang cổng khác (ví dụ: ```php -S localhost:8888```).

3. Khi dùng PHP ```built-in server```, server chỉ hoạt động khi ```terminal``` còn mở.

Ví dụ file ```index.php```

```<?php
echo "Chào mừng bạn đến với ứng dụng PHP!";
?>
```

4. Sau khi chạy server, truy cập ```http://localhost:8000``` sẽ thấy thông báo trên.

## Bạn có thể copy toàn bộ nội dung trên vào file
`README.md` của dự án. Nội dung này bao gồm 3 cách phổ biến để chạy file PHP và mở trên trình duyệt, kèm ví dụ cụ thể và lưu ý cần thiết.

