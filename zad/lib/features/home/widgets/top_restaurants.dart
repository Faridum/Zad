import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../models/restaurant_model.dart';

class TopRestaurants extends StatelessWidget {
  final List<Restaurant> restaurants;

  const TopRestaurants({
    super.key,
    required this.restaurants,
  });

  @override
  Widget build(BuildContext context) {
    if (restaurants.isEmpty) {
      return const SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // SECTION TITLE
        Text(
          'Top Grocery Restaurants',
          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 14),

        // LIST
        SizedBox(
          height: 110,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            itemCount: restaurants.length,
            separatorBuilder: (_, __) => const SizedBox(width: 14),
            itemBuilder: (context, index) {
              final restaurant = restaurants[index];

              return _RestaurantCard(restaurant: restaurant);
            },
          ),
        ),
      ],
    );
  }
}

class _RestaurantCard extends StatelessWidget {
  final Restaurant restaurant;

  const _RestaurantCard({required this.restaurant});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 90,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(10),
      child: Column(
        children: [
          // LOGO
          CircleAvatar(
            radius: 26,
            backgroundColor: AppTheme.inputFill,
            backgroundImage: restaurant.logo != null &&
                restaurant.logo!.isNotEmpty
                ? NetworkImage(restaurant.logo!)
                : null,
            child: restaurant.logo == null
                ? const Icon(Icons.store, color: AppTheme.greyLabel)
                : null,
          ),
          const SizedBox(height: 8),

          // NAME
          Text(
            restaurant.name,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              fontSize: 13,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}
