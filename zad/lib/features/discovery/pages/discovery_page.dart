import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../cart/pages/cart_page.dart';
import '../../cart/providers/cart_provider.dart';
import '../../home/providers/home_provider.dart';
import '../../home/widgets/top_restaurants.dart';
import '../widgets/discover_header.dart';
import '../widgets/discover_products_grid.dart';

class DiscoverPage extends ConsumerWidget {
  const DiscoverPage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final homeAsync = ref.watch(homeProvider);

    return SafeArea(
      child: homeAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(),
        ),
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
                DiscoverHeader(
                  cartCount: ref.watch(cartProvider).length,
                  onCartTap: () {
                    Navigator.of(context, rootNavigator: true).push(
                      MaterialPageRoute(builder: (_) => const CartPage()),
                    );
                  },
                ),
                const SizedBox(height: 24),
                TopRestaurants(restaurants: home.restaurants),

                const SizedBox(height: 28),
                Text(
                  'Discover Products',
                  style: Theme.of(context).textTheme.titleMedium,
                ),

                const SizedBox(height: 16),
                DiscoverProductsGrid(
                  products: home.products,
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

