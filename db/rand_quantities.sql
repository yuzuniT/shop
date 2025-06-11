/* 全ての行のstock_quantityに1~50のランダムな数字をセットする */

UPDATE shop.products
SET stock_quantity = FLOOR(RAND() * 50) + 1;