import 'package:flutter/material.dart';


class ProfileMenuItem extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? trailingText;
  final VoidCallback? onTap;
  final Color? titleColor;


  const ProfileMenuItem({
    super.key,
    required this.icon,
    required this.title,
    this.trailingText,
    this.onTap,
    this.titleColor,
  });


  @override
  Widget build(BuildContext context) {
    final effectiveColor = titleColor ?? Colors.black;


    return InkWell(
      onTap: onTap,
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 14),
            child: Row(
              children: [
                Icon(
                  icon,
                  size: 22,
                  color: effectiveColor,
                ),
                const SizedBox(width: 12),


                Expanded(
                  child: Text(
                    title,
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w500,
                      color: effectiveColor,
                    ),
                  ),
                ),


                if (trailingText != null)
                  Text(
                    trailingText!,
                    style: const TextStyle(
                      fontSize: 13,
                      color: Colors.grey,
                    ),
                  ),


                const SizedBox(width: 6),
                const Icon(
                  Icons.arrow_forward_ios,
                  size: 14,
                  color: Colors.grey,
                ),
              ],
            ),
          ),


          // ✅ Divider ناعم وخفيف
          Container(
            height: 0.6,
            margin: const EdgeInsets.only(right: 10,left: 10),
            // يبدأ بعد الأيقونة (لمسة احترافية)
            color: Colors.grey.withOpacity(0.25),
          ),
        ],
      ),
    );
  }
}

