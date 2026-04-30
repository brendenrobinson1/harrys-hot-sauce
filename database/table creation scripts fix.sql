ALTER TABLE HARRYS.Shopping_cart modify SC_unit_price dec(10,2);
ALTER TABLE HARRYS.Shopping_cart modify SC_discount_percentage dec(10,2);
ALTER TABLE HARRYS.Shopping_cart modify SC_discount_amount dec(10,2);

ALTER TABLE HARRYS.Shipping modify Shipping_freight_costs dec(10,2);
ALTER TABLE HARRYS.Shipping modify Shipping_handling_costs dec(10,2);

ALTER TABLE HARRYS.Products modify Products_unit_price dec(10,2);
ALTER TABLE HARRYS.Products modify Products_discounted_unit_price dec(10,2);

ALTER TABLE HARRYS.Order_headers modify Order_header_discount_percentage dec(10,2);
ALTER TABLE HARRYS.Order_headers modify Order_header_discount_amount dec(10,2);

ALTER TABLE HARRYS.Order_details modify Order_details_unit_price dec(10,2);
ALTER TABLE HARRYS.Order_details modify Order_details_discount_percentage dec(10,2);
ALTER TABLE HARRYS.Order_details modify Order_details_discount_amount dec(10,2);

ALTER TABLE HARRYS.Inventory modify Inventory_unit_cost dec(10,2);
ALTER TABLE HARRYS.Inventory modify Inventory_weight dec(10,2);
