// lib/features/home/providers/home_provider.dart
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../shared/services/api_service.dart';
import '../models/restaurant_model.dart';
import '../models/product_model.dart';

final homeProvider = FutureProvider<HomeData>((ref) async {
  final response = await ApiService.get("home/index.php");

  if (!response.success) {
    throw Exception(response.message);
  }

  final data = response.data as Map<String, dynamic>? ?? {};

  final restaurantsJson = data['restaurants'];
  final productsJson = data['products']; // ✅ الاسم الصحيح القادم من API

  return HomeData(
    restaurants: restaurantsJson is List
        ? restaurantsJson
        .map((e) => Restaurant.fromJson(e))
        .toList()
        : [],
    products: productsJson is List
        ? productsJson
        .map((e) => Product.fromJson(e))
        .toList()
        : [],
  );
});

/// DTO خاص بالـ Home
class HomeData {
  final List<Restaurant> restaurants;
  final List<Product> products;

  HomeData({
    required this.restaurants,
    required this.products,
  });
}

