<script setup>
import { ref, provide } from 'vue'
import PoCommentsIndex from '../comments/PoCommentsIndex.vue'

const props = defineProps({
  alone: { type: Boolean, default: true },
  data: { type: Object, required: true },
  likers: Object,
})

const loadingComments = ref(true)

provide('loadingComments', loadingComments)
provide('writingId', props.data.id)
</script>

<template>
  <v-card rounded elevation="2" class="mb-5">
    <template v-if="!$helper.isEmpty(data.extra_info) && !$helper.strNullOrEmpty(data.extra_info.cover)">
      <v-img class="align-end text-white" height="200" :src="$helper.storage(data.extra_info.cover)" cover>
        <div class="text-center py-3">
          <po-link :href="$route('users.show', data.author.username)" inertia>
            <po-avatar size="64" color="secondary" :user="data.author" />
          </po-link>
        </div>
      </v-img>
    </template>

    <div v-else class="text-center pt-3">
      <po-link :href="$route('users.show', data.author.username)" inertia>
        <po-avatar size="64" color="secondary" :user="data.author" />
      </po-link>
    </div>

    <v-card-text>
      <div class="text-center mb-3">
        <p class="text-h5 text-uppercase font-weight-bold">
          <po-link :href="$route('writings.show', data.slug)" inertia>{{ data.title }}</po-link>
        </p>
        <p class="text-caption text-uppercase font-weight-light">
          {{
            `${$t('main.by-name', { name: $helper.userDisplayName(data.author) })}
                    @ ${$helper.toLocaleDate(data.created_at)}`
          }}
        </p>
      </div>

      <template v-if="alone">
        <blockquote class="mb-3">
          {{ data.text }}
        </blockquote>

        <template v-if="!$helper.isEmpty(data.extra_info) && !$helper.strNullOrEmpty(data.extra_info.link)">
          <po-button variant="text" :href="data.extra_info.link" prepend-icon="fas fa-link" target="_blank"
            rel="nofollow noopener" class="mb-4">

            {{ $helper.cropUrl(data.extra_info.link) }}
          </po-button>
        </template>

        <div v-if="!$helper.isEmpty(data.categories)" class="d-flex mb-2">
          <div class="mr-3">
            <v-icon icon="fas fa-folder-open"></v-icon>
          </div>

          <div>
            <po-button v-for="category in data.categories" :key="category.slug" color="primary" size="x-small"
              :href="$route('categories.show', category.slug)" class="mr-1" inertia>
              {{ category.name }}
            </po-button>
          </div>
        </div>

        <div v-if="!$helper.isEmpty(data.tags)" class="d-flex mb-4">
          <div class="mr-3">
            <v-icon icon="fas fa-hashtag"></v-icon>
          </div>

          <div>
            <po-button v-for="tag in data.tags" :key="tag.slug" color="success" size="x-small"
              :href="$route('tags.show', tag.slug)" class="mr-1" inertia>
              {{ tag.name }}
            </po-button>
          </div>
        </div>

        <div v-if="!$helper.isEmpty(likers)">
          <p class="mb-3">{{ $t('main.liked-by') }}</p>

          <div class="d-inline-flex flex-wrap">
            <div v-for="liker in likers" :key="liker.id" class="mx-1 my-1">
              <po-button icon :href="$route('users.show', liker.username)" inertia>
                <po-avatar size="48" color="secondary" :user="liker" />
              </po-button>
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <blockquote>
          {{ $helper.excerpt(data.text) }}
        </blockquote>
      </template>
    </v-card-text>

    <v-divider></v-divider>
    <v-card-actions>
      <div class="d-flex w-100 mx-auto align-items-center text-caption text-center">
        <div v-if="!$helper.strNullOrEmpty(data.home_posted_at)" class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-fan" color="amber-accent-4"></v-icon></div>
            <div>:</div>
          </div>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-heart"></v-icon></div>
            <div>{{ $helper.readable(data.likes_count) }}</div>
          </div>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-comment"></v-icon></div>
            <div>{{ $helper.readable(data.comments_count) }}</div>
          </div>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-book-reader"></v-icon></div>
            <div>{{ $helper.readable(data.views) }}</div>
          </div>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-bookmark"></v-icon></div>
            <div>{{ $helper.readable(data.shelf_count) }}</div>
          </div>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-column">
            <div><v-icon icon="fas fa-dove"></v-icon></div>
            <div>{{ data.aura }}</div>
          </div>
        </div>
      </div>
    </v-card-actions>
  </v-card>

  <template v-if="alone">
    <v-skeleton-loader v-if="loadingComments" :elevation="3" type="list-item-avatar" class="mb-2"></v-skeleton-loader>
    <v-skeleton-loader v-if="loadingComments" :elevation="3" type="list-item-avatar" class="mb-2"></v-skeleton-loader>
    <v-skeleton-loader v-if="loadingComments" :elevation="3" type="list-item-avatar" class="mb-2"></v-skeleton-loader>

    <po-comments-index />
  </template>
</template>