document.addEventListener("DOMContentLoaded", () => {
    // Създаване на контейнер за преглед
    const preview = document.createElement("div");
    preview.id = "hoverPreview";
    preview.innerHTML = `
        <div class="preview-container">
            <img src="" alt="Преглед">
            <div class="preview-info">
                <h4 class="preview-title"></h4>
                <p class="preview-price"></p>
            </div>
        </div>
    `;
    document.body.appendChild(preview);
    
    // Добавяне на стилове
    const style = document.createElement("style");
    style.textContent = `
        #hoverPreview {
            position: absolute;
            display: none;
            width: 300px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 15px;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        
        .preview-container img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .preview-info {
            text-align: center;
        }
        
        .preview-title {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }
        
        .preview-price {
            margin: 0;
            color: #e60000;
            font-size: 18px;
            font-weight: bold;
        }
        
        .ranking-link.hovered {
            color: #e60000 !important;
            text-decoration: underline;
        }
    `;
    document.head.appendChild(style);
    
    const links = document.querySelectorAll(".ranking-link");
    
    // Предварително зареждане на изображения
    const preloadImages = [];
    links.forEach(link => {
        const imgSrc = link.dataset.image;
        if (imgSrc) {
            const img = new Image();
            img.src = imgSrc;
            preloadImages.push(img);
        }
    });
    
    links.forEach(link => {
        link.addEventListener("mouseenter", (e) => {
            const imgSrc = link.dataset.image;
            const title = link.textContent.trim();
            const price = link.dataset.price ? `${link.dataset.price} лв.` : '';
            
            preview.querySelector("img").src = imgSrc || 'assets/default-book.jpg';
            preview.querySelector(".preview-title").textContent = title;
            preview.querySelector(".preview-price").textContent = price;
            
            // Позициониране
            const x = e.pageX + 15;
            const y = e.pageY - 15;
            
            preview.style.left = x + "px";
            preview.style.top = y + "px";
            preview.style.display = "block";
            link.classList.add("hovered");
            
            // Проверка дали излиза от екрана и коригиране
            setTimeout(() => {
                const previewRect = preview.getBoundingClientRect();
                const windowWidth = window.innerWidth;
                const windowHeight = window.innerHeight;
                
                if (previewRect.right > windowWidth) {
                    preview.style.left = (x - previewRect.width - 30) + "px";
                }
                
                if (previewRect.bottom > windowHeight) {
                    preview.style.top = (y - previewRect.height - 30) + "px";
                }
            }, 10);
        });
        
        link.addEventListener("mouseleave", () => {
            preview.style.display = "none";
            link.classList.remove("hovered");
        });
        
        link.addEventListener("mousemove", (e) => {
            if (preview.style.display === "block") {
                const x = e.pageX + 15;
                const y = e.pageY - 15;
                
                preview.style.left = x + "px";
                preview.style.top = y + "px";
            }
        });
    });
    
    // Създаване на файл `get_cart_count.php` за обновяване на брояча
    console.log('Hover.js зареден успешно');
});
