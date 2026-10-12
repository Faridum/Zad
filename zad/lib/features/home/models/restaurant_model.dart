class Restaurant {
  final int id;
  final String name;
  final String? logo;
  final double? rating;

  Restaurant({
    required this.id,
    required this.name,
    this.logo,
    this.rating,
  });

  factory Restaurant.fromJson(Map<String, dynamic> json) {
    return Restaurant(
      id: json['id'],
      name: json['name'],
      logo: json['logo'],
      rating: json['rating'] != null
          ? double.parse(json['rating'].toString())
          : null,
    );
  }
}
