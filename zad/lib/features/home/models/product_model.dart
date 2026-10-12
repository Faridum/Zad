class Product {
  final int id;
  final int restaurantId;
  final String name;
  final double price;
  final double? salePrice;
  final String? photo;
  final String? description;


  Product({
    required this.id,
    required this.restaurantId,
    required this.name,
    required this.price,
    this.salePrice,
    this.photo,
    this.description,
  });


  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: int.parse(json['id'].toString()),
      restaurantId: int.parse(json['restaurant_id'].toString()),
      name: json['name'] ?? '',
      price: double.parse(json['price'].toString()),
      salePrice: json['sale_price'] != null
          ? double.tryParse(json['sale_price'].toString())
          : null,
      photo: json['photo'],
      description: json['description'],
    );
  }
}

