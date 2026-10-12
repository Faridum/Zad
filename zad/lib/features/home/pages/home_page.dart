import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/providers/user_profile_provider.dart';
import '../../cart/pages/cart_page.dart';
import '../../cart/providers/cart_provider.dart';
import '../providers/home_provider.dart';
import '../widgets/home_header.dart';
import '../../../shared/widgets/search_bar.dart';
import '../widgets/promo_banner.dart';
import '../widgets/top_restaurants.dart';
import '../../../shared/widgets/product_card.dart';
import '../../product/pages/product_details_page.dart';

class HomePage extends ConsumerWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final homeAsync = ref.watch(homeProvider);
    final userAsync = ref.watch(userProfileProvider);

    final userName = userAsync.when(
      data: (user) => user['name'] ?? 'Guest',
      loading: () => 'Loading...',
      error: (_, __) => 'Guest',
    );

    return SafeArea(
      child: homeAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(
          child: Text(
            e.toString(),
            style: const TextStyle(color: Colors.red),
          ),
        ),
        data: (home) {
          return SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 12),

                HomeHeader(
                  userName: userName,
                  location: "Amman, Jordan",
                  cartCount: ref.watch(cartProvider).length,
                  onCartTap: () {
                    Navigator.of(context, rootNavigator: true).push(
                      MaterialPageRoute(builder: (_) => const CartPage()),
                    );
                  },
                ),



                const SizedBox(height: 20),
                const AppSearchBar(),
                const SizedBox(height: 20),
                const PromoBanner(),
                const SizedBox(height: 28),

                TopRestaurants(restaurants: home.restaurants),

                const SizedBox(height: 28),
                Text(
                  "Recommended for you",
                  style: Theme.of(context)
                      .textTheme
                      .bodyMedium
                      ?.copyWith(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),

                const SizedBox(height: 14),

                SizedBox(
                  height: 230,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    itemCount: home.products.length,
                    separatorBuilder: (_, __) =>
                    const SizedBox(width: 12),
                    itemBuilder: (context, index) {
                      final product = home.products[index];

                      return GestureDetector(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) =>
                                  ProductDetailsPage(product: product),
                            ),
                          );
                        },
                        child: ProductCard(
                          product: product,
                        ),
                      );
                    },
                  ),
                ),

                const SizedBox(height: 40),
              ],
            ),
          );
        },
      ),
    );
  }
}

