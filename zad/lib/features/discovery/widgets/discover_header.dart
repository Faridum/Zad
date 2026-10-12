import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../shared/widgets/search_bar.dart';

class DiscoverHeader extends StatelessWidget {
  final VoidCallback? onCartTap;
  final int cartCount;

  const DiscoverHeader({super.key, this.onCartTap, required this.cartCount});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const Expanded(child: AppSearchBar()),
        Stack(
          clipBehavior: Clip.none,
          children: [
            IconButton(
              onPressed: onCartTap,
              icon: const Icon(Icons.shopping_bag_outlined),
            ),
            if (cartCount > 0)
              Positioned(
                right: 6,
                top: 6,
                child: Container(
                  padding: const EdgeInsets.all(4),
                  decoration: const BoxDecoration(
                    color: AppTheme.primary,
                    shape: BoxShape.circle,
                  ),
                  constraints: const BoxConstraints(
                    minWidth: 18,
                    minHeight: 18,
                  ),
                  child: Text(
                    cartCount.toString(),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
          ],
        ),
        Builder(
          builder: (ctx) => IconButton(
            icon: const Icon(Icons.menu),
            onPressed: () {
              Scaffold.of(ctx).openDrawer();
            },
          ),
        ),
      ],
    );
  }
}


