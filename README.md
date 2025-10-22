# Personal Notes App (Laravel + Vue.js)

Ứng dụng web ghi chú cá nhân với 2 phần chính:

1. **Deadline Notes (Task Management)**  
   - Ghi chú thông tin chung của deadline.  
   - Có thể thêm nhiều **sub-task** cho mỗi deadline.  
   - Sub-task có các thuộc tính như: nội dung, ngày đến hạn, trạng thái (in-progress, done, cancelled, overdue).
   - Trạng thái của deadline tự động cập nhật theo trạng thái **sub-task**  

2. **Notebook Notes (Vở ghi)**  
   - Ghi chú dạng văn bản, hỗ trợ phân loại theo chủ đề (ví dụ: môn học, lĩnh vực).  
   - Có thể tìm kiếm và lọc theo category.  
   - Hướng tới hỗ trợ gửi ảnh, file (trình soạn thảo WYSIWYG).  

---

## Công nghệ sử dụng
- **Backend**: Laravel  
- **Frontend**: Vue.js  
- **Database**: MySQL  
- **CSS**: TailwindCSS  

---

## Tính năng chính
- [x] Quản lý ghi chú Deadline (task + sub-task).  
- [x] Quản lý ghi chú Notebook (theo category).  
- [x] Tìm kiếm và lọc ghi chú.  
- [x] Đánh dấu trạng thái công việc.  
- [x] Giao diện thân thiện (Vue + TailwindCSS).  

---

## Hướng phát triển
- [ ] Thêm thông báo nhắc nhở deadline.  
- [x] Hỗ trợ Markdown / Rich Text Editor cho Notebook.  
- [ ] Gắn thẻ (tags) cho ghi chú.  
- [x] Xuất / nhập dữ liệu (CSV).   

---
